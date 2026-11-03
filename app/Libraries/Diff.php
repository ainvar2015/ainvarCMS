<?php

namespace App\Libraries;

class Diff
{
    /**
     * Compara dos textos palabra a palabra.
     * Devuelve: [['op' => 'eq'|'del'|'ins', 'text' => '...'], ...]
     * ('del' = estaba en $old y ya no está · 'ins' = está en $new y no estaba)
     */
    public static function words(string $old, string $new): array
    {
        $split = fn($t) => array_values(array_filter(preg_split('/\s+/u', trim($t)) ?: [], fn($w) => $w !== ''));
        $a = $split($old);
        $b = $split($new);
        $m = count($a);
        $n = count($b);

        // Texto enorme: no intentar diff fino, devolverlo como cambio total
        if ($m * $n > 2000000) {
            return [
                ['op' => 'del', 'text' => implode(' ', $a)],
                ['op' => 'ins', 'text' => implode(' ', $b)],
            ];
        }

        // Matriz LCS con SplFixedArray (8 bytes/celda en vez de ~50 de un array normal)
        $dp = new \SplFixedArray($m + 1);
        for ($i = 0; $i <= $m; $i++) {
            $dp[$i] = new \SplFixedArray($n + 1);
            for ($j = 0; $j <= $n; $j++) {
                $dp[$i][$j] = 0;
            }
        }

        for ($i = $m - 1; $i >= 0; $i--) {
            for ($j = $n - 1; $j >= 0; $j--) {
                $dp[$i][$j] = $a[$i] === $b[$j]
                    ? $dp[$i + 1][$j + 1] + 1
                    : max($dp[$i + 1][$j], $dp[$i][$j + 1]);
            }
        }

        // Recorrido inverso construyendo las operaciones
        $ops = [];
        $i = 0; $j = 0;
        while ($i < $m && $j < $n) {
            if ($a[$i] === $b[$j]) {
                $ops[] = ['op' => 'eq',  'text' => $a[$i]]; $i++; $j++;
            } elseif ($dp[$i + 1][$j] >= $dp[$i][$j + 1]) {
                $ops[] = ['op' => 'del', 'text' => $a[$i]]; $i++;
            } else {
                $ops[] = ['op' => 'ins', 'text' => $b[$j]]; $j++;
            }
        }
        while ($i < $m) { $ops[] = ['op' => 'del', 'text' => $a[$i]]; $i++; }
        while ($j < $n) { $ops[] = ['op' => 'ins', 'text' => $b[$j]]; $j++; }

        return self::compact($ops);
    }

    // Agrupa operaciones consecutivas del mismo tipo en un solo bloque
    protected static function compact(array $ops): array
    {
        $out = [];
        foreach ($ops as $op) {
            $last = end($out);
            if ($last && $last['op'] === $op['op']) {
                $out[key($out)]['text'] .= ' ' . $op['text'];
            } else {
                $out[] = $op;
            }
        }
        return $out;
    }
}