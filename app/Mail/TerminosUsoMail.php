<?php  

namespace App\Mail;  

use Illuminate\Bus\Queueable;  
use Illuminate\Mail\Mailable;  
use Illuminate\Queue\SerializesModels;  

class TerminosUsoMail extends Mailable  
{  
    use Queueable, SerializesModels;  

        // Modifica el Mailable  
public $userName;  
public $terminosTexto;  

public function __construct($userName, $terminosTexto)  
{  
    $this->userName = $userName;  
    $this->terminosTexto = $terminosTexto;  
} 

    public function build()  
    {  
        return $this->subject('Términos de Uso Aceptados')  
                    ->view('emails.terminos_uso')  
                    ->with([  
                        'userName' => $this->userName,  
                    ]);  
    }  

}