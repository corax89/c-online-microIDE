<?php

$start = microtime(true);
$input = $_POST['input'];
$optimization = $_POST['optimization'];
$setcompiler = $_POST['setcompiler'];
$codepage = $_POST['code_page'];

$out='';

//выполняем скрипт если в запросе есть исходный код
if(isset($_POST['text_box'])) {

	$a = $_POST['text_box'];
	$filename='tmp/program.c';
	$myfile = fopen($filename, "w") or die("Невозможно открыть файл для записи!");

	fwrite($myfile, $a);
	fclose($myfile);
    
    //добавляем параметры оптимизации
    switch($optimization){
        case 1:
            $optstring = ' -O2';
            break;
        case 2:
            $optstring = ' -Os';
            break;
        default:
            $optstring = ' -O0';
    }
    
    switch($codepage){
        case 1:
            $setcodepage = 'cp1251';
            break;
        case 2:
            $setcodepage = 'cp866';
            break;
        default:
            $setcodepage = 'utf8';
    }
    
    //компилируем выбранным компилятором
    if($setcompiler==0){
	    exec('gcc '
	        . $filename
	        . $optstring
	        . ' -o tmp/program  -finput-charset=utf8 -fexec-charset='
	        . $setcodepage
	        . ' 2>&1', $outputErr, $return_value);
	    echo 'Размер файла: ' . filesize('tmp/program') . ' байт. ';
    }
	else{
	    exec('i586-mingw32msvc-gcc '
	        . $filename
	        . $optstring
	        . ' -o tmp/program.exe  -finput-charset=utf8 -fexec-charset='
	        . $setcodepage
	        . ' 2>&1', $outputErr, $return_value);
	    echo 'Размер файла: ' . filesize('tmp/program.exe') . ' байт';
	}
    
	//проверка на наличие ошибок компиляции
	if($return_value!=0){
		$out = $out . 'ERROR: ' . PHP_EOL . implode(PHP_EOL, $outputErr);
	}
	else{
	    //разрешаем файлу выполняться
		exec('chmod 777 '.'tmp/program');
		$inputFile='tmp/output.txt';
		exec ('chmod 777 '.$inputFile);
		$file = fopen($inputFile, "w") or die("Невозможно открыть файл для записи!");
		
		fwrite($file, $input);
		fclose($file);

		exec('./tmp/program<'.$inputFile, $output, $return_value);
		//получаем результат выполнения программы
		$out = $out . PHP_EOL . implode(PHP_EOL, $output);
		$time = microtime(true) - $start;
        printf('Время компиляции %.4F сек.', $time);
	}
	unlink($filename);
	unlink($inputFile);
}

echo $out;