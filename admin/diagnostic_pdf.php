<?php
require(dirname(__FILE__, 2) .'/vendor/setasign/fpdf/fpdf.php');
require(dirname(__FILE__, 5) .'/wp-blog-header.php');
require(dirname(__FILE__, 5) .'/wp-load.php');

if (!defined('ABSPATH')) {
    exit;
}
class DiagnosticPDF extends FPDF {
    private $info;
    private $op;
    public function __construct() {
        $this->info = $this->phpinfo2array();
        $this->op = Onepay::getInstance();
        parent::__construct();
    }
// Page header
    function Header()
    {
        // Logo
       # $this->Image('logo.png',10,6,30);s
        // Arial bold 15
        $this->SetFont('Arial','B',15);
        // Move to the right
        $this->Cell(60);
        // Title
        $this->Cell(80,10,utf8_decode('Diagnostic information'),1,0,'C');
        // Line break
        $this->Ln(20);
    }

    function addPHPVersion() {
        // Move to the right
        $this->Cell(10);
        // Title
        $this->Cell(40,15,utf8_decode('PHP Version'),0,0,'L');
        $this->Ln(15);

        $phpversion = phpversion();
        $minVersion = "5.5.0";

        // Move to the right
        $this->Cell(20);

        $higherOrEqualToMinVersion = version_compare($phpversion, $minVersion, ">=");
        $status = $higherOrEqualToMinVersion ? "OK" : "Versión no soportada";
        $this->Cell(40,8,'Status: ' . $status, 0,0,'L');
        // Line break
        $this->Ln(8);
        $this->Cell(20);
        $this->Cell(40,8,'PHP version: ' . $phpversion, 0,0,'L');
        // Line break
        $this->Ln(8);
    }


    function addServerApi() {
        // Move to the right
        $this->Cell(10);
        // Title
        $this->Cell(40,15,utf8_decode('Server version'),0,0,'L');
        $this->Ln(15);

        // Move to the right
        $this->Cell(20);
        $server_version = $this->info['phpinfo']['Server API'];
        $this->Cell(20,8,'Server software: ' . $server_version, 0,0,'L');
        // Line break
        $this->Ln(8);
    }
    function addWooCommerceInfo() {
        global $woocommerce;

        // Move to the right
        $this->Cell(10);
        // Title
        $this->Cell(40,15,utf8_decode('Plugin info'),0,0,'L');
        $this->Ln(15);

        // Move to the right
        $this->Cell(20);
        $woocommerce_version = $woocommerce->version;
        $this->Cell(20,8,'Ecommerce: Woocommerce', 0,0,'L');
        // Line break
        $this->Ln(8);
        // Move to the right
        $this->Cell(20);
        $this->Cell(20,8,'Ecommerce version: ' . $woocommerce_version, 0,0,'L');
        $this->Ln(8);
        // Line break
        // Move to the right
        $this->Cell(20);
        $this->Cell(20,8,'Current Onepay plugin version: ' . $this->op->get_version(), 0,0,'L');
        // Line break
        $this->Ln(8);
    }

    function addMerchantInfo() {
        global $woocommerce, $wp;
        $this->Ln(8);
        // Move to the right
        $this->Cell(10);
        $this->Cell(20, 8, 'Merchant info');
        $this->Ln(8);
        $api_key = $this->op->get_option( 'apikey' );
        $env = $this->op->get_option('endpoint');
        $this->Ln(8);

        $this->Cell(20);
        $this->Cell(20,8,'ApiKey: ' . $api_key, 0,0,'L');
        $this->Ln(8);
        $this->Cell(20);
        $this->Cell(20,8,'Environment: ' . $env, 0,0,'L');
        $this->Ln(8);
    }

    function addExtensionsInfo() {
        $this->Ln(8);
        // Move to the right
        $this->Cell(10);
        $this->Cell(20, 8, 'Extensions');
        $this->Ln(8);
        $extensions = get_loaded_extensions();
        foreach($extensions as $ext ) {
            $extVersion = phpversion($ext);
            // Move to the right
            $this->Cell(20);
            $this->Cell(0,10, $ext . ' : ' . $extVersion,0,1);
        }

    }


    function addLogs() {
        $this->Ln(8);
        $this->Cell(10, 8, 'Logs');
        $this->Ln(15);
        $logfile = fopen(ABSPATH . 'wp-content/debug.log', "r");
        if ($logfile) {
            while (($line = fgets($logfile)) !== false) {
                // process the line read.
                $this->Write(10,  $line);
                $this->Ln(4);
            }

            fclose($logfile);
        } else {
            // error opening the file.
            $this->Cell(10, 8, 'debug.log could not be found.');
        }
    }
    // Page footer
    function Footer()
    {
        // Position at 1.5 cm from bottom
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial','I',8);
        // Page number
        $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
    }
    function phpinfo2array() {
        $entitiesToUtf8 = function($input) {
            // http://php.net/manual/en/function.html-entity-decode.php#104617
            return preg_replace_callback("/(&#[0-9]+;)/", function($m) { return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES"); }, $input);
        };
        $plainText = function($input) use ($entitiesToUtf8) {
            return trim(html_entity_decode($entitiesToUtf8(strip_tags($input))));
        };
        $titlePlainText = function($input) use ($plainText) {
            return '# '.$plainText($input);
        };

        ob_start();
        phpinfo(-1);

        $phpinfo = array('phpinfo' => array());

        // Strip everything after the <h1>Configuration</h1> tag (other h1's)
        if (!preg_match('#(.*<h1[^>]*>\s*Configuration.*)<h1#s', ob_get_clean(), $matches)) {
            return array();
        }

        $input = $matches[1];
        $matches = array();

        if(preg_match_all(
            '#(?:<h2.*?>(?:<a.*?>)?(.*?)(?:<\/a>)?<\/h2>)|'.
            '(?:<tr.*?><t[hd].*?>(.*?)\s*</t[hd]>(?:<t[hd].*?>(.*?)\s*</t[hd]>(?:<t[hd].*?>(.*?)\s*</t[hd]>)?)?</tr>)#s',
            $input,
            $matches,
            PREG_SET_ORDER
        )) {
            foreach ($matches as $match) {
                $fn = strpos($match[0], '<th') === false ? $plainText : $titlePlainText;
                if (strlen($match[1])) {
                    $phpinfo[$match[1]] = array();
                } elseif (isset($match[3])) {
                    $keys1 = array_keys($phpinfo);
                    $phpinfo[end($keys1)][$fn($match[2])] = isset($match[4]) ? array($fn($match[3]), $fn($match[4])) : $fn($match[3]);
                } else {
                    $keys1 = array_keys($phpinfo);
                    $phpinfo[end($keys1)][] = $fn($match[2]);
                }

            }
        }

        return $phpinfo;
    }
}




$pdf = new DiagnosticPDF();


//print_r($pdf->phpinfo2array());

$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times','',12);

// Add a title for the section
$pdf->Cell(60,15,utf8_decode('Server summary'),0,0,'L');
$pdf->Ln(15);
// Add php version
$pdf->addPHPVersion();
// Add server software
$pdf->addServerApi();
// Add plugin info
$pdf->addWooCommerceInfo();
// Add merchant info
$pdf->addMerchantInfo();
//Add extension info
$pdf->addExtensionsInfo();
$pdf->addLogs();

$pdf->Output();
