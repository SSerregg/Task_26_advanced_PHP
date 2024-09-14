<?php


class HtmlIterator implements Iterator {

    const ROW_SIZE = 4096;

    protected $filePointer = null;

    protected $currentElement = null;

    protected $rowCounter = null;

    protected $lastElement = 'first';

    public function __construct($file) {
     try {
         $this->filePointer = fopen($file, 'r');
     } catch (\Exception $e) {
         throw new \Exception('The file "' . $file . '" cannot be read.');
     }
    }

    public function rewind(): void {
       
        $this->rowCounter = 0;
        rewind($this->filePointer);
    }

    #[ReturnTypeWillChange] public function current() {
        
        $this->lastElement = $this->currentElement ?? 'first';
        $this->currentElement = fgets($this->filePointer, self::ROW_SIZE);
        $this->rowCounter++;

        return [$this->lastElement, $this->currentElement];
    }

    public function key(): int {
        
        return $this->rowCounter;
    }

    #[ReturnTypeWillChange] public function next(): bool{
        
        if (is_resource($this->filePointer)) {
            return !feof($this->filePointer);
        }

        return false;
    }

    public function valid(): bool{
       
        if (!$this->next()) {
            if (is_resource($this->filePointer)) {
                
                fclose($this->filePointer);
            }

            return false;
        }

        return true;
    }
}

$page = new HtmlIterator('page.html');

$changedPage = fopen('changedPage.html','w+');

foreach($page as $key => $value) {

    $matched = preg_match(
        '/<\bmeta\b[\s]+name="keywords|description"/i',
        $value[1]);
    $matchedMeta = preg_match(
            '/<\bmeta\b[\s]+name="keywords|description"/i',
            $value[0]);
    $matchedClose = preg_match(
            '/">\Z/i',
            $value[0]);
    $matchedTitle = preg_match('/<\/title>\Z/i', $value[1]);
    
   if($matchedMeta && !$matchedClose){

    } else{
        if(!$matched){
        if(!$matchedTitle){
        fwrite($changedPage, $value[1]);
        }
        }
    }

}
fclose($changedPage);

//include_once 'changedPage.html';