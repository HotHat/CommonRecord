<?php declare(strict_types=1);

// case 1
class MyException extends Exception {
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
    
    public function setFile($file) { $this->file = $file;}
    public function setLine($line) { $this->line = $line;}
}

// case 2
//set_error_handler(function ($no, $message, $file, $line, $context) {
//    echo '---------------------';
//    //var_dump($context);
//    echo '---------------------';
//    $exception = new MyException($message, $no);
//    $exception->setFile($file);
//    $exception->setLine($line);
//    throw new $exception;
//
//}, E_ALL);

// case 3
//set_exception_handler(function ($e){
//    var_dump($e);
//});

/*
 Lists of Throwable and Exception tree as of 7.2.0

    Error
      ArithmeticError
        DivisionByZeroError
      AssertionError
      ParseError
      TypeError
        ArgumentCountError
    Exception
      ClosedGeneratorException
      DOMException
      ErrorException
      IntlException
      LogicException
        BadFunctionCallException
          BadMethodCallException
        DomainException
        InvalidArgumentException
        LengthException
        OutOfRangeException
      PharException
      ReflectionException
      RuntimeException
        OutOfBoundsException
        OverflowException
        PDOException
        RangeException
        UnderflowException
        UnexpectedValueException
      SodiumException
 */


// case 4
register_shutdown_function(function (){
    var_dump(error_get_last());
    //$trace = debug_backtrace();
    //var_dump($trace);
});

//try {
    
    include "error_file.php";
//} catch (\Exception $e) {
    //echo 1234;
    //throw $e;
//}




echo "\n" . 'end';

