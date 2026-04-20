<?php  

namespace App\Mail;  

use Illuminate\Bus\Queueable;  
use Illuminate\Mail\Mailable;  
use Illuminate\Queue\SerializesModels;  

class ReceptorXmlMail extends Mailable  
{  
    use Queueable, SerializesModels;  

    protected $zipFilePath;  

    public function __construct($zipFilePath)  
    {  
        $this->zipFilePath = $zipFilePath;  
    }  

    public function build()  
    {  
        return $this->subject('Archivo ZIP de Recepciones')  
                    ->view('emails.receptor_xml') // Asegúrate de tener esta vista creada  
                    ->attach($this->zipFilePath);  
    }  
}