<?php  

class FileUtil {

    /**
     * Handles multiple file uploads
     * and stores it in the specified
     * destination
     * 
     * @param array $files The files that will be uploaded
     * @param string $destination The destination that the files will be stored in
     * @param string $prefix The prefix that will be added to the filename to avoid file overriding
     * @param string $filename The file name of the file
     * @param bool $usePrefix Should use prefix or not
     * @return array The new filepaths of the uploaded files
     */
    public static function uploadFiles(array $files, string $destination, ?string $prefix, string $filename = '', bool $usePrefix = true) : array {
        if(!is_dir($destination))
            mkdir($destination, 0777, true);

        if(($prefix == null || empty($prefix)) && $usePrefix)
            $prefix = date('Y-m-d h-i-s');

        $paths = [
            'paths' => [],
            'response' => [
                'hasError' => false,
                'message' => "Failed to upload "
            ]
        ];

        if(!is_array($files['name'])) {
            $uploadPath = $destination . ($usePrefix ? "/${prefix}_" : "/") . str_replace(" ", "_", empty($filename) ? basename($files['name']) : $filename);
            $tempFile = $files['tmp_name'];

            if(move_uploaded_file($tempFile, $uploadPath))
                array_push($paths['paths'], $uploadPath);
            else {
                $paths['response']['hasError'] = true;
                $paths['response']['message'] .= empty($filename) ? basename($files['name']) : $filename;
            }
        } else {
            for($i = 0; $i < count($files['name']); $i++) {
                $filename = "${i}${filename}";
                $uploadPath = $destination .($usePrefix ? "/${prefix}_" : "/") . str_replace(" ", "_", empty($filename) ? basename($files['name'][$i]) : $filename);
                $tempFile = $files['tmp_name'][$i];

                if($tempFile == "" || $tempFile == " ")
                    continue;

                if(move_uploaded_file($tempFile, $uploadPath))
                    array_push($paths['paths'], $uploadPath);
                else {
                    $paths['response']['hasError'] = true;
                    $paths['response']['message'] .= empty($filename) ? basename($files['name'][$i]) : $filename . ', ';
                }
            }

            if(substr($paths['response']['message'], strlen($paths['response']['message']) - 2) == ", ")
                $paths['response']['message'] = substr($paths['response']['message'], 0, strlen($paths['response']['message']) - 2);
        }

        return $paths;
    }

    /**
     * Reads a file line by line
     * and returns its contents
     * as string
     * 
     * @param string $filepath The filepath of the file to be read
     * @param int $buffer The buffer length
     * @return string
     */
    public static function readFile(string $filepath, int $buffer) : string {
        $contents = "";
        $handle = @fopen($filepath, "r");

        if($handle) {
            while(!feof($handle)) 
                $contents .= fgets($handle, $buffer);

            fclose($handle);
        }

        return $contents;
    }

    /**
     * Writes to a file
     * 
     * @param string $filepath The filepath of the file
     * @param string $content The content to write on the file
     * @param string $mode The write mode, default 'w'
     */
    public static function writeFile(string $filepath, string $content, string $mode = 'w') {
        $handle = @fopen($filepath, $mode);

        if($handle) {
            fwrite($handle, $content);
            fclose($handle);
        }
    }
}

?>