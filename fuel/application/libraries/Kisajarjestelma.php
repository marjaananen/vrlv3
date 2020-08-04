<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
class Kisajarjestelma
{

    public function __construct()
        {
        }
        
    
    public function sijoittuu($osallistujia, $jaos_id){
    
        $sijoittuu = 0;
        //NJ:ssä ei sijoituta samalla tavalla.
        if ( $jaos_id == 7 ) { $sijoittuu = 0; }
        else if( $osallistujia >= 1 AND $osallistujia <= 3 ) 	{	$sijoittuu = 1;	} 
        elseif( $osallistujia >= 4 AND $osallistujia <= 8 ) 	{	$sijoittuu = 2;	} 
        elseif( $osallistujia >= 9 AND $osallistujia <= 15 ) 	{	$sijoittuu = 3;	} 
        elseif( $osallistujia >= 16 AND $osallistujia <= 24 )	{	$sijoittuu = 4;	} 
        elseif( $osallistujia >= 25 AND $osallistujia <= 35 ) 	{	$sijoittuu = 5;	} 
        elseif( $osallistujia >= 36 AND $osallistujia <= 48 ) 	{	$sijoittuu = 6;	} 
        elseif( $osallistujia >= 49 AND $osallistujia <= 63 ) 	{	$sijoittuu = 7;	} 
        elseif( $osallistujia >= 64 AND $osallistujia <= 80 ) 	{	$sijoittuu = 8;	} 
        elseif( $osallistujia >= 81 AND $osallistujia <= 99 ) 	{	$sijoittuu = 9;	} 
        elseif( $osallistujia >= 100) 							{	$sijoittuu = 10; }
        else { $sijoittuu = 0; }
        
        return $sijoittuu;
                
    }

    //sisältää kaikki vanhat ja uudet arvontatavat, myös käytöstä poistuneet
    public function arvontatavat_options_legacy(){
        $arvontatavat = array (1 => "Lyhyt arvonta",
                               2 => "Suhteutettu arvonta",
                               4 => "Tarina/kysymys",
                               3=> "Porrastettu arvonta",
                               5=> "Tuomarointi (NJ)"); 
         return $arvontatavat;                                                                                                                        //
                                                                                                                                 
    
    }
}