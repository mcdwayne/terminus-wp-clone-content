<?php

use Pantheon\Terminus\Commands\TerminusCommand;
// use Pantheon\Terminus\Site\SiteAwareInterface;
// use Pantheon\Terminus\Site\SiteAwareTrait;
// use Pantheon\Terminus\Collections\Sites;
// use Pantheon\Terminus\Exceptions\TerminusException;


class WPCloneContentCommand extends TerminusCommand
{
    /**
     * Print the classic message to the log.
     *
     * @command wpclonecontent
     *
	 * @param string $originsite_originenv Site & environment in the format `site-name.env` where content lives
	 * @param string $targetsite_targetenv Site & environment in the format `site-name.env` were we writing it to
     * @param string $postid Need a post ID, we will ask for this if not set.  
     *
     * @usage terminus wpclonecontent <originsite>.<originenv> <targetsite>.<targetenv> <postid>
     */


     
    public function wpCloneContent($originsite_originenv, $targetsite_targetenv,$postid)
    {
   		
    	// $POSTID = '580' ;

    	

// DO NOT TOUCH BELOW THIS LINE!!!!!!!!!!!!!!!!!!!!!
		ob_start();

	    passthru(" terminus wp $originsite_originenv -- post get $postid --format=json") ;
	     	    
	    $TEMPVALUE = ob_get_contents() ;
		
		ob_clean();

		$POSTDATA = json_decode($TEMPVALUE);

//make a temp file to read from - having issues with tmpfile, so let's make a real file. 
		$myfile = fopen("testfile.txt", "w") ; 
		fwrite($myfile, $POSTDATA->post_content) ;
		rewind($myfile);
		$temp_file = realpath('testfile.txt');
		chmod($temp_file, 0644)  ;
		fclose($myfile);

		$TEMPSTRING=' --post_author='.$POSTDATA->post_author.' --post_title=\''.$POSTDATA->post_title.'\' --post_excerpt=\''.$POSTDATA->post_excerpt.'\' --post_status='.$POSTDATA->post_status.' --post_type='.$POSTDATA->post_type.' --post_name=\''.$POSTDATA->post_name.'\' --menu_order='.$POSTDATA->menu_order.' - ' ;
	    ob_clean();

// actually create th new post 

	    passthru(" cat $temp_file | terminus wp $targetsite_targetenv -- post create $TEMPSTRING --porcelain") ;


// This is the only way I manipulated the output that worked.  

	    $POSTIDTEMP1 = ob_get_contents() ;
	    $POSTSTRING=(string)$POSTIDTEMP1 ;
	    $POSTID2=substr($POSTSTRING, 0, 3) ;
 		ob_clean();
	    // echo $POSTID2 ;

// // Featured Image Clone 
		// ob_clean();
		// passthru('terminus env:wake $originsite_originenv') ;
 		ob_clean();

 		$TEMPSTRING1 = 'post meta get '.$postid.' _thumbnail_id';
		passthru("terminus wp $originsite_originenv -- $TEMPSTRING1 ");

		$ORIGINALHEROIMAGEID= ob_get_contents() ;

	    $ORIGINALHEROIMADEIDSTRING=(string)$ORIGINALHEROIMAGEID ;
	    $CLEANORIGINALHEROIMADEID=preg_replace( "/\r|\n/", "", $ORIGINALHEROIMADEIDSTRING );
 ;
 		
		$TEMPSTRING3 = 'post get '.$CLEANORIGINALHEROIMADEID.' --format=json';
		// echo $TEMPSTRING3 ;
 		ob_clean();
 		passthru(" terminus wp $originsite_originenv -- $TEMPSTRING3 ") ;
	     	    
	    $HEROIMAGEMETA = ob_get_contents() ;
		$HEROIMAGEMETAJSON = json_decode($HEROIMAGEMETA) ;
		ob_clean();



 		$TEMPSTRING2 = 'post meta get '.$CLEANORIGINALHEROIMADEID.' _wp_attachment_image_alt';
		passthru("terminus wp $originsite_originenv -- $TEMPSTRING2 ");

		$ALTTEXT= ob_get_contents() ;

		ob_clean();
		
		$TEMPGUID=(string)$HEROIMAGEMETAJSON->guid ;
		$TEMPTITLE=(string)$HEROIMAGEMETAJSON->post_name ;
		$TEMPALT1=(string)$ALTTEXT ;
		$TEMPALT=preg_replace( "/\r|\n/", "", $TEMPALT1 ); ;

	$BUILDVAR='media import '.$TEMPGUID.' --post_id='.$POSTID2.' --title=\''.$TEMPTITLE.'\' --alt=\''.$TEMPALT.'\' --featured_image ' ;
	passthru(" terminus wp $targetsite_targetenv -- $BUILDVAR " ); 

//   Got to deal with Tags

		ob_clean();
		$BUILDTAGVAR= "terminus wp ".$originsite_originenv." -- post term list ".$postid." post_tag --format=json" ;
  		passthru("$BUILDTAGVAR");

  		$TAGSVAR = ob_get_contents() ;
		$TAGSVARJSON = json_decode($TAGSVAR) ;
		$tagstring = "" ;

  foreach($TAGSVARJSON as $mytag)

    {
    //this will build all terms into a string that looks like -> "term1" "term2" "termn"
        $tagstring .= "\"".$mytag->name."\" "  ;
    
    } 

    	$BUILDTAGVAR1 = "post term add ".$POSTID2." post_tag ".$tagstring ;
		
		echo "\n" ;
		echo $tagstring ;
		echo "\n" ;

		passthru(" terminus wp $targetsite_targetenv -- $BUILDTAGVAR1 " );     


	
	// // only works if a new post_name to target env, otherwise fails  
	// 	echo $TARGETENV.'-'.$TARGETSITENAME.'.pantheonsite.io/'.$POSTDATA->post_name ;
      	echo "\n\n\n" ; ;
    
    }
}





?>