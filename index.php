<?php

class TotalCommanderPasswordDecoder
{
    private int $random_seed = 0;

    public static function hexstr2bytearray($str)
    {
        if (!is_string($str)) return false;

        $result = [];

        $len = strlen($str);

        if ($len == 0 || ($len & 1) != 0)
        {
            return false;
        }

        for ($i = 0; $i < $len; $i += 2)
        {
            $result[] = ( hexdec($str[$i]) << 4 ) | hexdec($str[$i + 1]);
        }

        return $result;
    }

    // initialize random generator with specified seed
    public function srand($seed)
    {
        $this->random_seed = $seed;
    }

    // generate pseudo-random number from the specified seed
    public function rand_max($nMax)
    {
        // cut numbers to 32 bit values (important)
        $this->random_seed = (( ($this->random_seed * 0x8088405) & 0xFFFFFFFF) + 1) & 0xFFFFFFFF;

        return ($this->random_seed * $nMax) >> 32;
    }

    // rotate bits left
    public static function rol8($var, $counter)
    {
        return (($var << $counter) | ($var >> (8 - $counter))) & 0xFF;
    }

    // decrypt Total Commander FTP password
    public function decryptPassword($password)
    {
        // convert hex string to array of integers
        $password_hex = static::hexstr2bytearray($password);

        // if the conversion failed - exit
        if (!$password_hex) return false;

        // number of converted bytes
        $password_length = count($password_hex);

        // length includes checksum at the end
        if ($password_length <= 4)
        {
            return false;
        }

        // minus checksum
        $password_length -= 4;

        $this->srand(849521);

        for ($i = 0; $i < $password_length; $i++)
        {
            $password_hex[ $i ] = static::rol8($password_hex[ $i ], $this->rand_max(8));
        }

        $this->srand(12345);

        for ($i = 0; $i < 256; $i++)
        {
            $x = $this->rand_max($password_length);
            $y = $this->rand_max($password_length);

            $c = $password_hex[ $x ];

            $password_hex[ $x ] = $password_hex[ $y ];
            $password_hex[ $y ] = $c;
        }

        $this->srand(42340);

        for($i = 0; $i < $password_length; $i++)
        {
            $password_hex[ $i ] ^= $this->rand_max(256);
        }

        $this->srand(54321);

        for ($i = 0; $i < $password_length; $i++)
        {
            $password_hex[ $i ] = ($password_hex[ $i ] - $this->rand_max(256)) & 0xFF;
        }

        // build final password
        $decoded_password = "";

        for($i = 0; $i < $password_length; $i++)
        {
            $decoded_password .= chr($password_hex[ $i ]);
        }

        return $decoded_password;
    }
}

$totalCommanderFile = __DIR__ . "/wcx_ftp.ini";
$outputFile = __DIR__ . "/output.csv";
if(file_exists($outputFile))
{
    unlink($outputFile);
}

if (file_exists($totalCommanderFile))
{
    $fileContent = file_get_contents($totalCommanderFile);

    if ($fileContent !== false)
    {
        $lines = explode("\n", $fileContent);

        $currentSection = "";
        $currentHost = "";
        $currentUsername = "";
        $currentPassword = "";

        $totalCommanderPasswordDecoder = new TotalCommanderPasswordDecoder();

        foreach ($lines as $line)
        {
            $line = trim($line);

            if (empty($line))
            {
                continue;
            }

            if ($line[0] === '[' && $line[strlen($line) - 1] === ']')
            {
                $currentSection = substr($line, 1, strlen($line) - 2);
            }
            else
            {
                $parts = explode("=", $line);

                if (count($parts) === 2)
                {
                    $key = trim($parts[0]);
                    $value = trim($parts[1]);

                    if ($key === "host")
                    {
                        $currentHost = $value;
                    }
                    else if ($key === "username")
                    {
                        $currentUsername = $value;
                    }
                    else if ($key === "password")
                    {
                        $currentPassword = $value;
                    }
                }
            }

            if (!empty($currentHost) && !empty($currentUsername) && !empty($currentPassword))
            {
                $decodedPassword = $totalCommanderPasswordDecoder->decryptPassword($currentPassword);

                if ($decodedPassword !== false)
                {

                    file_put_contents($outputFile, $currentSection . ";" . $currentHost . ";" . $currentUsername . ";" . $decodedPassword . "\n", FILE_APPEND);
                }

                $currentHost = "";
                $currentUsername = "";
                $currentPassword = "";
            }
        }
    }
}
else
{
    echo "File not found: " . $totalCommanderFile . "\n";
}

//create xml import file for FileZilla from csv with plain text passwords
$csvFile = __DIR__ . "/output.csv";
$xmlFile = __DIR__ . "/filezilla.xml";
if(file_exists
($xmlFile))
{
    unlink($xmlFile);
}

if (file_exists($csvFile))
{
    $file = fopen($csvFile, "r");

    if ($file !== false)
    {
        $xml = new XMLWriter();
        $xml->openURI($xmlFile);
        $xml->startDocument('1.0', 'UTF-8');
        $xml->setIndent(true);
        $xml->startElement('FileZilla3');
        $xml->startElement('Servers');

        while (($line = fgetcsv($file, 0, ";")) !== false)
        {
            $xml->startElement('Server');
            $xml->writeElement('Host', $line[1]);
            $xml->writeElement('Port', '21');
            $xml->writeElement('Protocol', '0');
            $xml->writeElement('Type', '0');
            $xml->writeElement('User', $line[2]);
            $xml->writeElement('Pass', $line[3]);
            $xml->writeElement('Logontype', '1');
            $xml->writeElement('TimezoneOffset', '0');
            $xml->writeElement('PasvMode', 'MODE_DEFAULT');
            $xml->writeElement('MaximumMultipleConnections', '0');
            $xml->writeElement('EncodingType', 'Auto');
            $xml->writeElement('BypassProxy', '0');
            $xml->writeElement('Name', $line[0]);
            $xml->endElement();
        }

        $xml->endElement();
        $xml->endElement();
        $xml->endDocument();
        $xml->flush();
    }
}
else
{
    echo "File not found: " . $csvFile . "\n";
}
