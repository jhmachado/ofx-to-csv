<?php

class Ofx
{
    /** @var string */
    private $filePath;
    
    public function __construct($filePath)
    {
        $this->filePath = $filePath;
    }
    
    /**
     * Convert the OFX file to XML
     */
    public function getOfxAsXML()
    {
        $content = file_get_contents($this->filePath);
        $line = strpos($content, "<OFX>");
        $ofx = substr($content, $line - 1);
        $buffer = $ofx;
        $count = 0;
        
        while ($pos = strpos($buffer, '<')) {
            $count++;
            $pos2 = strpos($buffer, '>');
            $element = substr($buffer, $pos + 1, $pos2 - $pos - 1);
            
            if (substr($element, 0, 1) == '/')
                $sla[] = substr($element, 1);
            else
                $als[] = $element;
                
            $buffer = substr($buffer, $pos2 + 1);
        }
        
        $adif = array_diff($als, $sla);
        $adif = array_unique($adif);
        $ofxy = $ofx;
        foreach ($adif as $dif) {
            $dpos = 0;
            
            while ($dpos = strpos($ofxy, $dif, $dpos + 1)) {    
                $npos = strpos($ofxy, '<', $dpos + 1);
                $ofxy = substr_replace($ofxy, "</$dif>\n<", $npos, 1);
                
                $dpos = $npos + strlen($element) + 3;
            
            }
        
        }
        
        $ofxy = str_replace('&', '&', $ofxy);
        
        return $ofxy;    
    }
    
    /**
     * Returns the account balance at the file export date
     */
    public function getBalance()
    {    
        $xml = new SimpleXMLElement($this->getOfxAsXML());
        $balance = $xml->BANKMSGSRSV1->STMTTRNRS->STMTRS->LEDGERBAL->BALAMT;
        $dateNode = $xml->BANKMSGSRSV1->STMTTRNRS->STMTRS->LEDGERBAL->DTASOF;
        $date = strtotime(substr($dateNode, 0, 8));
        $dateToReturn = date('Y-m-d', $date);
        
        return [
            'date' => $dateToReturn,
            'balance' => $balance
        ];
    }
    
    /**
     * Returns a list of objects with the transactions
     *  DTPOSTED => Transaction date
     *  TRNAMT   => Transaction amount
     *  TRNTYPE  => Transaction Type (Debit or Credit) 
     *  MEMO     => Description
     */
    public function getTransactions() {
        $xml = new SimpleXMLElement($this->getOfxAsXML());
        
        $transactions = $xml->BANKMSGSRSV1->STMTTRNRS->STMTRS->BANKTRANLIST->STMTTRN;
        
        return $transactions;
    }
}
