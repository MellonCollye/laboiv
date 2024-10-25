public class BaseInteraction{
    private $response;
    private $header;

    public function set_response($newResponse){
        $this->$response = $newResponse;
    }

    public function set_header($newHeader){
        $this->$header = $newHeader;
    }

    public function send(){
        if($this->$header){
            header($header);
        }
        if($this->$response){
            echo json_encode();
        }
    }
}

public class Error extends BaseInteraction{

    function __construct($newResponse, $newHeader){
        $this->set_header($newHeader);
        $this->set_response(['error' -> $newResponse]);
        send();
    }

    function __construct($newResponse){
        $this->set_response(['error' -> $newResponse]);
        send();
    }
}

public class Response extends BaseInteraction{

    function __construct($newResponse){
        $this->set_header('Content-Type: application/json');
        $this->$response = $newResponse;
        send();
    }
}