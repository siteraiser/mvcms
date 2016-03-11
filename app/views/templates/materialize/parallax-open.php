<div class="parallax-container">
    <div class="parallax"><?php
	
	if(strlen($content)>2){
	$content=strip_tags($content, '<img>');
	$doc = new DOMDocument();
	@$doc->loadHTML($content);

	$doc->removeChild($doc->doctype); 
	$doc->replaceChild($doc->firstChild->firstChild->firstChild, $doc->firstChild);
	//fix images
	$imgs = $doc->getElementsByTagName('img');	
	//$total=$imgs->length;
	for ($i = $imgs->length; --$i >= 0; ) {		  
		$img = $imgs->item($i);		
		$img->removeAttribute('style');
		$img->setAttribute('class','clean');
	}
	 $content =$doc->saveHTML();
	echo$content;
	}
	?></div>

