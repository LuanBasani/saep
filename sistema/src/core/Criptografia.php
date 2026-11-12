<?php
declare(strict_types=1);

namespace App\Core;

final class Criptografia {
    //Classe para criptografia do CPF
     private static function obterChave(): string {
        $chave = getenv('SISVET_APP_KEY');
        if ($chave !== false && strlen($chave) >= 32) {
            return hash('sha256', $chave, true);
        }

        $arquivoChave = dirname(__DIR__, 2) . '/config/app.key';
        if (!is_file($arquivoChave)) {
            $novaChave = bin2hex(random_bytes(32));
            if (@file_put_contents($arquivoChave, $novaChave, LOCK_EX) === false) {
                throw new \RuntimeException('Não foi possível criar a chave de criptografia em sistema/config/app.key.');
            }
            @chmod($arquivoChave, 0600);
        }

        $chave = trim((string)file_get_contents($arquivoChave));
        if (strlen($chave) < 32) {
            throw new \RuntimeException('A chave em sistema/config/app.key é inválida.');
        }

        return hash('sha256', $chave, true);
    }

    /**
     * Cifra uma string gerando IV aleatorio embutido no resultado Base64.
     */
    public static function cifrar(string $dado): string {
        $iv = random_bytes(16);
        $cifrado = openssl_encrypt($dado, 'aes-256-cbc', self::obterChave(), 0, $iv);
        return base64_encode($iv . $cifrado);
    }

    /**
     * Decifra uma string codificada em Base64 extraindo seu IV correspondente.
     */
    public static function decifrar(string $dadoCifrado): string {
        $dadosBrutos = base64_decode($dadoCifrado, true);
        if ($dadosBrutos === false || strlen($dadosBrutos) <= 16) {
            throw new \UnexpectedValueException('CPF criptografado inválido.');
        }
        $iv = substr($dadosBrutos, 0, 16);
        $texto = substr($dadosBrutos, 16);
        $resultado = openssl_decrypt($texto, 'aes-256-cbc', self::obterChave(), 0, $iv);
        if ($resultado === false) {
            throw new \UnexpectedValueException('Não foi possível descriptografar o CPF.');
        }
        return $resultado;
    }
}
