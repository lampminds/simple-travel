<?php

/**
 * One-off: MD -> RTF for inversor doc (minimal markdown).
 */
declare(strict_types=1);

$mdPath = __DIR__ . '/inversor-proyecto-tecnica.md';
$outPath = __DIR__ . '/inversor-proyecto-tecnica.rtf';
$altPath = __DIR__ . '/inversor-proyecto-tecnica-temp.rtf';

$md = file_get_contents($mdPath);
if ($md === false) {
    fwrite(STDERR, "Cannot read {$mdPath}\n");
    exit(1);
}
$md = str_replace(["\r\n", "\r"], "\n", $md);

function rtfUnicode(string $s): string
{
    $s = str_replace(['\\', '{', '}'], ['\\', '\{', '\}'], $s);
    $r = '';
    $len = strlen($s);
    for ($i = 0; $i < $len; $i++) {
        $o = ord($s[$i]);
        if ($o < 128) {
            $r .= $s[$i];
            continue;
        }
        if (($o & 0xE0) === 0xC0 && $i + 1 < $len) {
            $c2 = $s[++$i];
            $code = (($o & 0x1F) << 6) | (ord($c2) & 0x3F);
            $r .= '\\u' . $code . '?';
        } elseif (($o & 0xF0) === 0xE0 && $i + 2 < $len) {
            $c2 = $s[++$i];
            $c3 = $s[++$i];
            $code = (($o & 0x0F) << 12) | ((ord($c2) & 0x3F) << 6) | (ord($c3) & 0x3F);
            if ($code > 32767) {
                $code -= 65536;
            }
            $r .= '\\u' . $code . '?';
        } else {
            $r .= $s[$i];
        }
    }
    return $r;
}

function inlineMd(string $s): string
{
    $s = preg_replace_callback(
        '/\*\*(.+?)\*\*/s',
        static fn (array $m) => '\b ' . rtfUnicode($m[1]) . '\b0 ',
        $s
    ) ?? $s;
    $s = preg_replace_callback(
        '/\*(.+?)\*/s',
        static fn (array $m) => '\i ' . rtfUnicode($m[1]) . '\i0 ',
        $s
    ) ?? $s;
    $s = preg_replace_callback(
        '/`(.+?)`/',
        static fn (array $m) => '\b ' . rtfUnicode($m[1]) . '\b0 ',
        $s
    ) ?? $s;

    return rtfUnicode($s);
}

$lines = explode("\n", $md);
$rtf = [];
$rtf[] = '{\rtf1\ansi\deff0{\fonttbl{\f0\froman Times New Roman;}}';
$rtf[] = '\f0\fs24';

$inTable = false;
foreach ($lines as $line) {
    if (preg_match('/^#+\s+(.+)$/', $line, $m)) {
        if ($inTable) {
            $inTable = false;
            $rtf[] = '\par ';
        }
        $level = strlen($line) - strlen(ltrim($line, '#'));
        $t = rtfUnicode($m[1]);
        $size = 32 - (min($level, 2) * 2);
        // Use single-quoted parts: in double-quoted "\f" is the form-feed character, breaking \fs (font size).
        $rtf[] = '\par\fs' . $size . '\b ' . $t . '\b0\par\fs24';
        continue;
    }
    if (str_starts_with($line, '|') && str_contains($line, '---')) {
        continue;
    }
    if (str_starts_with($line, '|')) {
        if (! $inTable) {
            $inTable = true;
        }
        $row = array_map('trim', explode('|', $line));
        $row = array_values(array_filter($row, static fn ($c) => $c !== ''));
        $rtf[] = '\par ' . inlineMd(implode('    |    ', $row)) . ' ';
        continue;
    }
    if ($inTable) {
        $inTable = false;
        $rtf[] = '\par ';
    }
    if (trim($line) === '---') {
        $rtf[] = '\par\brdrb\brdrs\brdrw10\par\fs24';
        continue;
    }
    if (preg_match('/^[-*]\s+(.+)$/', $line, $m)) {
        $rtf[] = '\par\bullet\t' . inlineMd($m[1]);
        continue;
    }
    if (preg_match('/^\d+\.\s+(.+)$/', $line, $m)) {
        $rtf[] = '\par ' . inlineMd($m[0]);
        continue;
    }
    if (trim($line) === '') {
        $rtf[] = '\par ';
        continue;
    }
    $rtf[] = '\par ' . inlineMd($line);
}
$rtf[] = "\par}";

$body = implode('', $rtf);
if (file_put_contents($outPath, $body) === false) {
    if (file_put_contents($altPath, $body) !== false) {
        echo "Could not write {$outPath} (file may be open). Wrote {$altPath} — close Word and replace, or run again.\n";
    } else {
        fwrite(STDERR, "Failed to write RTF output.\n");
        exit(1);
    }
} else {
    echo "Wrote {$outPath}\n";
}
