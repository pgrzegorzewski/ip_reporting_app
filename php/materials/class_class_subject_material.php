<?php 
 
class ClassSubjectMaterial
{
    public $materialText;
    function __construct($class, $subject)
    {
        if($class == '7' && $subject == 'english')
        {
            $this->printMaterialEnglishClass7();
        }
        elseif($class == '7' && $subject == 'chemistry')
        {
            $this->printMaterialChemistryClass7();
        }
        elseif($class == '7' && $subject == 'biology')
        {
            $this->printMaterialBiologyClass7();
        }
        elseif($class == '7' && $subject == 'polish')
        {
            $this->printMaterialPolishClass7();
        }
        else {
           $this->materialText = "Niestety chwilowo nie ma tutaj materiałów";
        }
    }
    
    function printMaterialEnglishClass7()
    {
        
        
        $this->materialText = '
                                  <div>
                                    <div class="image">
                                        <img src="../resources/img/materials/uk_flag.png" width="200" />
                                    </div>
                                    <h3>Powtórka z czasów</h3>
                                    <h4>Present Simple</h4>
                                    <span>Present Simple to czas teraźniejszy prosty</span>
                                    <br/>Używamy go gdy:<br/>
                                    <ul>
                                        <li>Mówimy o czynnściach wykonywanych regularnie</li>
                                        <li>Stanch stałych</li>
                                        <li>Zjawiskach, które zawsze są prawdziwe (przykładowo słońce wschodzi każdego dnia)</li>
                                        <li>Czynnościach trwających dłuższy okres czasu</li>
                                    </ul>
                                    <br/>
                                    <span><b>Konstrukcja zdania:</b></span><br/>
                                    <span>Zdanie składa się z podmiotu + czasownika w formie osobowej (w trzeciej osobie liczby pojedynczej z końcówką -s, -es, lub -ies) + reszta zdania, w której zazwyczaj zawarty jest określnik czasu</span>
                                    <br/><br>
                                    <h4>Przykłady zdań:</h4>
                                    <span><b>Zdanie twierdzące:</b></span>
                                    <br/><span>I go to school five times a week</span>
                                    <br/><span>She goes to school five times a week</span>
                                    <br/><br/><span><b>Zdanie przeczące:</b></span>
                                    <br><span>I don\'t play piano as well as Tom</span>
                                    <br/><span>She doesn\'t play piano as well as Tom</span>
                                    <br/><br/><span><b>Zdanie pytające:</b></span>
                                    <br/><span>Do you like comedies?</span>
                                    <br/><span>Deos she like comedies?</span>
                                    <br/>
                                  </div>
                                  <div class = \'video\'>
                                    <iframe width="560" height="315" src="https://www.youtube.com/embed/jppW4UByWOc" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
                                  </div>
                                ';
    }
    
    function printMaterialChemistryClass7()
    {
        
        
        $this->materialText = '
                                  
                                  <div>
                                    <div class="image">
                                        <img src="../resources/img/materials/Tablica_Mendelejewa.png" width="1100" />
                                    </div>     
                                  </div>
                                  <br /><br />
                                  <h3>Typy rekacji chemicznych</h3>
                                  <table>
                                        <tr style="background-color:#CAAB15; color:#28546C">
                                            <td>Zapis reakcji</td>
                                            <td>Typ reakcji</td>
                                        </tr>
                                        <tr>
                                            <td><b>A + B -> AB</b></td>
                                            <td>Reakcja syntezy - z dwóch lub więcej substancji prostych powstaje substancja złożona</td>
                                        </tr>
                                        <tr>
                                            <td><b>AB -> A + B</b></td>
                                            <td>Reakcja analizy (rozkładu) - z substancji złożona rozkładana jest na jedną lub więcej substancj prostych</td>
                                        </tr>
                                        <tr>
                                            <td><b>AB + C -> AC + B</b></td>
                                            <td>Reakcja wymiany pojedynczej - z dwóch lub więcej substancji powstają inne, przy czym jedna z nich jest prostsza</td>
                                        </tr>
                                        <tr>
                                            <td><b>AB + CD -> AD + CB</b></td>
                                            <td>Reakcja wymiany podwójnej - z dwóch substancji powstają inne, przy czym wszystkie reagenty są substancjami złożonymi.</td>
                                        </tr>
                                    </table>
                                ';
    }
    
    function printMaterialBiologyClass7()
    {
        
        
        $this->materialText = '
            
                                  <div>
                                    <div class="image">
                                        <img src="../resources/img/materials/Fotosynteza.png" width="1100" />
                                    </div>
                                  </div>
                                  <br /><br />
                                  <h3>Fotosynteza</h3>
                                  <span>Proces wytwarzania związków organicznych z materii nieorganicznej zachodzących w komórkach zawierających chlorofil przy udziale światła.
                                  <br>Wyróżnia się dwie fazy: 
                                  <br><b>faza jasna</b> - absorbowane jest światło, którego energia zmianiana jest na energię wiązań chemicznych a produktem ubocznym jest tlen.
                                  <br><b>faza ciemna</b> - energia z fazy jasnej jest wykorzystywana do syntezy związków organicznych</span>
                                ';
    }
    
    function printMaterialPolishClass7()
    {
        
        
        $this->materialText = '
                                  <br /><br />
                                    <div class="image">
                                        <img src="../resources/img/materials/pen.png" width="250"/>
                                    </div>
                                  <h3>Szur i kot - Ignacy Krasicki</h3>
                                  <p style ="text-align:center">"Mnie to kadzą" - rzekł hardzie do swego rodzeństwa
                                  <br/>Siedząc szczur na ołtarzu podczas nabożeństwa.
                                  <br/>Wtem, gdy się dymem kadzidł zbytecznych zakrztusił,
                                  <br/>Wpadł kot z boku na niego, porwał i udusił.</p>

                                  <br /><br />
                                  <h3>Na Zdrowie - Jan Kochanowski</h3>
                                  <p style ="text-align:center">Ślachetne zdrowie, <br>Nikt się nie dowie, <br>Jako smakujesz, <br>Aż się zepsujesz. <br>Tam człowiek prawie <br>Widzi na jawie <br>I sam to powie, <br>Że nic nad zdrowie <br>Ani lepszego, <br>Ani droższego;
Bo dobre mienie, <br>Perły, kamienie, <br>Także wiek młody <br>I dar urody, <br>Mieśca wysokie, <br>Władze szerokie <br>Dobre są, ale - <br>Gdy zdrowie w cale. <br>Gdzie nie masz siły, <br>I świat niemiły. <br>Klinocie drogi, <br>Mój dom ubogi <br>Oddany tobie <br>Ulubuj sobie!</p>
                                ';
    }
    
    
    function getMaterialText()
    {
        return $this->materialText;
    }
    
}



?>