<?php
// includes/rsa_manual.php - Implementación manual de RSA

class RSAManual {
    
    // Números primos pequeños para generar claves (en producción usar primos más grandes)
    private static $primos = [
        61, 67, 71, 73, 79, 83, 89, 97, 101, 103, 107, 109, 113, 127, 131, 137, 139, 149,
        151, 157, 163, 167, 173, 179, 181, 191, 193, 197, 199, 211, 223, 227, 229, 233,
        239, 241, 251, 257, 263, 269, 271, 277, 281, 283, 293, 307, 311, 313, 317, 331,
        337, 347, 349, 353, 359, 367, 373, 379, 383, 389, 397, 401, 409, 419, 421, 431,
        433, 439, 443, 449, 457, 461, 463, 467, 479, 487, 491, 499, 503, 509, 521, 523,
        541, 547, 557, 563, 569, 571, 577, 587, 593, 599, 601, 607, 613, 617, 619, 631,
        641, 643, 647, 653, 659, 661, 673, 677, 683, 691, 701, 709, 719, 727, 733, 739,
        743, 751, 757, 761, 769, 773, 787, 797, 809, 811, 821, 823, 827, 829, 839, 853,
        857, 859, 863, 877, 881, 883, 887, 907, 911, 919, 929, 937, 941, 947, 953, 967,
        971, 977, 983, 991, 997
    ];

    // Generar un número aleatorio
    public static function random($min, $max) {
        return mt_rand($min, $max);
    }

    // Calcular el máximo común divisor
    public static function gcd($a, $b) {
        while ($b != 0) {
            $temp = $b;
            $b = $a % $b;
            $a = $temp;
        }
        return $a;
    }

    // Algoritmo extendido de Euclides para encontrar el inverso modular
    public static function extendedGCD($a, $b) {
        if ($a == 0) {
            return array($b, 0, 1);
        }
        
        list($gcd, $x1, $y1) = self::extendedGCD($b % $a, $a);
        $x = $y1 - intval($b / $a) * $x1;
        $y = $x1;
        
        return array($gcd, $x, $y);
    }

    // Calcular el inverso modular
    public static function modInverse($a, $m) {
        list($gcd, $x, $y) = self::extendedGCD($a, $m);
        if ($gcd != 1) {
            return null; // El inverso no existe
        }
        return ($x % $m + $m) % $m;
    }

    // Exponenciación modular (a^b mod m)
    public static function modPow($base, $exp, $mod) {
        if ($mod == 1) return 0;
        
        $result = 1;
        $base = $base % $mod;
        
        while ($exp > 0) {
            if ($exp % 2 == 1) {
                $result = ($result * $base) % $mod;
            }
            $exp = $exp >> 1;
            $base = ($base * $base) % $mod;
        }
        
        return $result;
    }

    // Generar un par de claves RSA
    public static function generarClaves() {
        // Seleccionar dos números primos aleatorios
        $p = self::$primos[self::random(0, count(self::$primos) - 1)];
        $q = self::$primos[self::random(0, count(self::$primos) - 1)];
        
        // Asegurar que p y q sean diferentes
        while ($p == $q) {
            $q = self::$primos[self::random(0, count(self::$primos) - 1)];
        }

        // Calcular n = p * q
        $n = $p * $q;

        // Calcular phi(n) = (p-1)(q-1)
        $phi = ($p - 1) * ($q - 1);

        // Seleccionar e (exponente público) - comúnmente se usa 65537 o un número pequeño
        $e = 65537;
        // Si e es muy grande para nuestros primos, usar un valor más pequeño
        if ($e >= $phi) {
            $e = 17;
            if ($e >= $phi) {
                $e = 3;
            }
        }

        // Asegurar que gcd(e, phi) = 1
        while (self::gcd($e, $phi) != 1) {
            $e += 2;
            if ($e >= $phi) {
                $e = 3;
            }
        }

        // Calcular d (exponente privado)
        $d = self::modInverse($e, $phi);

        if ($d === null) {
            // Si no se puede calcular d, intentar con otros primos
            return self::generarClaves();
        }

        return array(
            'publica' => array('n' => $n, 'e' => $e),
            'privada' => array('n' => $n, 'd' => $d),
            'detalles' => array('p' => $p, 'q' => $q, 'phi' => $phi)
        );
    }

    // Cifrar un número
    public static function cifrarNumero($mensaje, $clavePublica) {
        $n = $clavePublica['n'];
        $e = $clavePublica['e'];
        return self::modPow($mensaje, $e, $n);
    }

    // Descifrar un número
    public static function descifrarNumero($cifrado, $clavePrivada) {
        $n = $clavePrivada['n'];
        $d = $clavePrivada['d'];
        return self::modPow($cifrado, $d, $n);
    }

    // Convertir texto a números
    public static function textoANumeros($texto) {
        $numeros = array();
        for ($i = 0; $i < strlen($texto); $i++) {
            $numeros[] = ord($texto[$i]);
        }
        return $numeros;
    }

    // Convertir números a texto
    public static function numerosATexto($numeros) {
        $texto = '';
        foreach ($numeros as $num) {
            $texto .= chr($num);
        }
        return $texto;
    }

    // Cifrar texto completo
    public static function cifrarTexto($texto, $clavePublica) {
        $numeros = self::textoANumeros($texto);
        $cifrados = array();
        
        foreach ($numeros as $num) {
            // Asegurar que el número sea menor que n
            if ($num >= $clavePublica['n']) {
                // Si el carácter es mayor que n, usar un método de padding
                $cifrados[] = $num; // Mantener sin cifrar si es muy grande
            } else {
                $cifrados[] = self::cifrarNumero($num, $clavePublica);
            }
        }
        
        return implode(',', $cifrados);
    }

    // Descifrar texto completo
    public static function descifrarTexto($textoCifrado, $clavePrivada) {
        $cifrados = explode(',', $textoCifrado);
        $numeros = array();
        
        foreach ($cifrados as $cifrado) {
            $cifrado = intval($cifrado);
            if ($cifrado >= $clavePrivada['n']) {
                // Si no fue cifrado, mantener el valor original
                $numeros[] = $cifrado;
            } else {
                $numeros[] = self::descifrarNumero($cifrado, $clavePrivada);
            }
        }
        
        return self::numerosATexto($numeros);
    }

    // Serializar clave para almacenar en base de datos
    public static function serializarClave($clave) {
        return base64_encode(json_encode($clave));
    }

    // Deserializar clave desde base de datos
    public static function deserializarClave($claveSerializada) {
        return json_decode(base64_decode($claveSerializada), true);
    }
}
?>