<?php

/**
 * Markov chain class to generate random strings from source
 *
 * @example
 *
 *     // echo a markov chain from file
 *     $markov = new Markov();
 *     $markov->setSourceFile('/path/to/story');
 *     echo $markov->generate(20);
 *
 * @author Sean Sullivan
 */
Class Markov
{
    /**
     * Source text to generate from
     *
     * @var string
     */
    protected $text = '';

    public static function instance($file=null) {
        return new self($file);
    }

    public function __construct($file) {
        if($file != null) {
            $this->setSourceFile($file);
        }
    }

    /**
     * Read and set source text from a file
     *
     * @param string $file absolute path to file
     * @return void
     * @author Sean Sullivan
     */
    public function setSourceFile($file) {
        if(!file_exists($file)) {
            throw new Exception(sprintf('File not found: %s', $file));
        }

        $handle = fopen($file,'r');

        $this->text = fread($handle, filesize($file));
    }

    /**
     * Set source text from string
     *
     * @param string $string
     * @return void
     * @author Sean Sullivan
     */
    public function setSourceString($string) {
        $this->text = $string;
    }

    /**
     * Retrieve source text
     *
     * @return void
     * @author Sean Sullivan
     */
    public function source() {
        return $this->text;
    }

    /**
     * Generate Markov chain from source
     *
     * @param string $combo
     * @param string $num_words
     * @return void
     * @author Sean Sullivan
     */
    public function generate($num_words){
        if(strlen($this->text) == 0) {
            throw new Exception('Markov can not generate with text');
        }

        $output = "";
        $gran = 2;
        $num_words;
        $letters_line=50;

        // clean up and randomize the input text
        $input = preg_replace('/\s\s+/', ' ', $this->text);
        $input = preg_replace('/\n|\r/', '', $input);
        $input = strip_tags($input);
        $input = htmlentities($input);
        $input = explode(".",$input);
        shuffle($input);
        $input = implode(".", $input);

        $textwords = explode(" ", $input);
        $loopmax = count($textwords) - ($gran - 2) - 1;

        $frequency_table = array();

        for ($j = 0; $j < $loopmax; $j++) {
            $key_string = "";
            $end = $j + $gran;
            for ($k = $j; $k < $end; $k++) {
                $key_string .= $textwords[$k].' ';
            }
            $frequency_table[$key_string] = '';
            if(isset($textwords[$j+$gran])){
                $frequency_table[$key_string] .= $textwords[$j + $gran]." ";
            }
            if (($j+$gran) > $loopmax ) {
                break;
            }
        }

        $buffer = "";

        $lastwords = array();

        for ($i = 0; $i < $gran; $i++) {
            $lastwords[] = $textwords[$i];
            $buffer .= " ".$textwords[$i];
        }

        for ($i = 0; $i < $num_words; $i++) {
            $key_string = "";
            for ($j = 0; $j < $gran; $j++) {
                $key_string .= $lastwords[$j]." ";
            }
            if ($frequency_table[$key_string]) {
                $possible = explode(" ", trim($frequency_table[$key_string]));
                mt_srand();
                $c = count($possible);
                $r = mt_rand(1, $c) - 1;
                $nextword = $possible[$r];
                $buffer .= " $nextword";
                if (strlen($buffer) >= $letters_line) {
                    $output .= $buffer;
                    $buffer = "";
                }
                for ($l = 0; $l < $gran - 1; $l++) {
                    $lastwords[$l] = $lastwords[$l + 1];
                }
                $lastwords[$gran - 1] = $nextword;
            }
            else {
                $lastwords = array_splice($lastwords, 0, count($lastwords));
                for ($l = 0; $l < $gran; $l++) {
                    $lastwords[] = $textwords[$l];
                    $buffer .= ' '.$textwords[$l];
                }
            }
        }

        $output = trim($output);

        return $output;
    }
}

?>