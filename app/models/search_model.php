<?php /*
Copyright © 2016 
	
	This file is part of MVCMS.

    MVCMS is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    MVCMS is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with MVCMS.  If not, see <http://www.gnu.org/licenses/>.
*/	
class search_model extends requestHandler{

//Stopwords from: http://xpo6.com/download-stop-word-list/
	public $stopwords = array("a", "about", "above", "above", "across", "after", "afterwards", "again", "against", "all", "almost", "alone", "along", "already", "also","although","always","am","among", "amongst", "amoungst", "amount",  "an", "and", "another", "any","anyhow","anyone","anything","anyway", "anywhere", "are", "around", "as",  "at", "back","be","became", "because","become","becomes", "becoming", "been", "before", "beforehand", "behind", "being", "below", "beside", "besides", "between", "beyond", "bill", "both", "bottom","but", "by", "call", "can", "cannot", "cant", "co", "con", "could", "couldnt", "cry", "de", "describe", "detail", "do", "done", "down", "due", "during", "each", "eg", "eight", "either", "eleven","else", "elsewhere", "empty", "enough", "etc", "even", "ever", "every", "everyone", "everything", "everywhere", "except", "few", "fifteen", "fify", "fill", "find", "fire", "first", "five", "for", "former", "formerly", "forty", "found", "four", "from", "front", "full", "further", "get", "give", "go", "had", "has", "hasnt", "have", "he", "hence", "her", "here", "hereafter", "hereby", "herein", "hereupon", "hers", "herself", "him", "himself", "his", "how", "however", "hundred", "ie", "if", "in", "inc", "indeed", "interest", "into", "is", "it", "its", "itself", "keep", "last", "latter", "latterly", "least", "less", "ltd", "made", "many", "may", "me", "meanwhile", "might", "mill", "mine", "more", "moreover", "most", "mostly", "move", "much", "must", "my", "myself", "name", "namely", "neither", "never", "nevertheless", "next", "nine", "no", "nobody", "none", "noone", "nor", "not", "nothing", "now", "nowhere", "of", "off", "often", "on", "once", "one", "only", "onto", "or", "other", "others", "otherwise", "our", "ours", "ourselves", "out", "over", "own","part", "per", "perhaps", "please", "put", "rather", "re", "same", "see", "seem", "seemed", "seeming", "seems", "serious", "several", "she", "should", "show", "side", "since", "sincere", "six", "sixty", "so", "some", "somehow", "someone", "something", "sometime", "sometimes", "somewhere", "still", "such", "system", "take", "ten", "than", "that", "the", "their", "them", "themselves", "then", "thence", "there", "thereafter", "thereby", "therefore", "therein", "thereupon", "these", "they", "thickv", "thin", "third", "this", "those", "though", "three", "through", "throughout", "thru", "thus", "to", "together", "too", "top", "toward", "towards", "twelve", "twenty", "two", "un", "under", "until", "up", "upon", "us", "very", "via", "was", "we", "well", "were", "what", "whatever", "when", "whence", "whenever", "where", "whereafter", "whereas", "whereby", "wherein", "whereupon", "wherever", "whether", "which", "while", "whither", "who", "whoever", "whole", "whom", "whose", "why", "will", "with", "within", "without", "would", "yet", "you", "your", "yours", "yourself", "yourselves", "the");	
	//Build entire pages reverse word index
	public function updateIndex ($pages,$articles){
		$indexes=array();

		foreach($pages as $id => $array){
			foreach($array['words'] as $word){
			//	echo 'key:'.$id .'-'. $word . '<br>';
				if(!array_key_exists($word,$indexes ) ){
					$indexes[$word]['pages'] = $id;
				}else if(!in_array($id, explode(',',$indexes[$word]['pages'] ))){
					$indexes[$word]['pages'] = $indexes[$word]['pages'] . ',' .$id;
				}				
			}
		}
		
		
		foreach($articles as $id => $array){
			foreach($array['words'] as $word){
			//	echo 'key:'.$id .'-'. $word . '<br>';
				if(!array_key_exists($word,$indexes )){
					$indexes[$word]['articles'] = $id;
				}else if(!in_array($id, explode(',',$indexes[$word]['articles'] ))){
					$indexes[$word]['articles'] = $indexes[$word]['articles'] . ($indexes[$word]['articles']==''?'':',') .$id;
				}				
			}
		}
		
				
	//echo '<pre>',htmlspecialchars(print_r($articles, true)),'</pre>';
	//echo '<pre>',htmlspecialchars(print_r($pages, true)),'</pre>';
	//echo '<pre>',htmlspecialchars(print_r($indexes, true)),'</pre>';

		$this->pdo->query("TRUNCATE TABLE searchindex");	
		foreach($indexes as $word => $array){

			$sql="INSERT INTO searchindex (word,pagelist,articlelist) VALUES (?,?,?);";
			$stmt=$this->pdo->prepare($sql);
			$stmt->execute(array($word,$array['pages'],$array['articles']));	
		}
	}	

	public function updateSearch(){//Site search update execution starts here, use this function in your mvc application to include articles from the content table.
	
	$sql="SELECT *,pages.id AS pageid FROM content
		JOIN pages ON (pages.articleids=content.id AND pages.published = 1 AND content.published = 1
		) OR ( FIND_IN_SET(content.id, pages.articleids) AND pages.published = 1 AND content.published = 1)";

		$sql.="";
		$stmt=$this->pdo->prepare($sql);
		$stmt->execute(array());
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		$skip_headline=array('header','menu','footer');
		foreach($rows as $row)
		{	
			
			$content=$this->strip($row['content']);
			if( $content != ''){	
				if($row['type'] !='noindex'){					
					$content = $this->getUniques( str_replace("-", " ", (in_array($row['type'],$skip_headline)?:$row['headline'])).' '. $content);
				}else{
					$content = '';
				}
				if(isset($pages[$row['pageid']])){
					foreach($content as $word){
						$pages[$row['pageid']]['words'][] = $word;
					}
				}else{
					$pages[$row['pageid']]['words'] =  $content;
				}
			}			
		}
		//Get articles
		$sql="SELECT * FROM content WHERE (type != 'header' AND type != 'footer' AND type != 'default' AND type != 'menu' AND type != 'auto p false' AND type != 'noindex' AND type != '' AND type IS NOT NULL)";//type = article
		$stmt=$this->pdo->prepare($sql);
		$stmt->execute(array());
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		foreach($rows as $row)
		{	
			
			$content=$this->strip($row['content']);
			if( $content != ''){	
				if($row['type'] !='noindex'){					
					$content = $this->getUniques( $row['articlename'].' '. $row['description'].' '.$content);
				}else{
					$content = '';
				}
				if(isset($articles[$row['id']])){
					foreach($articles as $word){
						$articles[$row['id']]['words'][] = $word;
					}
				}else{
					$articles[$row['id']]['words'] =  $content;
					//$pages[$row['id']]['type'] = 'page';
				}
			}			
		}
			
		
		$this->updateIndex ($pages,$articles);
		//echo '<pre>',htmlspecialchars(print_r($pages, true)),'</pre>';
		//echo '<pre>',htmlspecialchars(print_r($articles, true)),'</pre>';
	}

	public function getUniques($content){
	
		$words = explode(' ',$content);
		$uniques=array();
		foreach($words as $word){
		$word = $this->wordStem(strtolower($word));
			if(!in_array( $word,$uniques ) && !in_array( $word,$this->stopwords ) && $word!=''){
				$uniques[] = $word;
			} 
		}
		//echo '<pre>',htmlspecialchars(print_r($uniques, true)),'</pre>';
		return $uniques;
	}

	function strip($html){

		$html = preg_replace('~<\s*\bscript\b[^>]*>(.*?)<\s*\/\s*script\s*>~is', '', $html);//remove scripts
		$html = preg_replace('~<\s*\bstyle\b[^>]*>(.*?)<\s*\/\s*style\s*>~is', '', $html);
		$html = preg_replace('~<\s*\bnav\b[^>]*>(.*?)<\s*\/\s*nav\s*>~is', '', $html);
		$html = preg_replace('#<br\s*/?>#i', "\n", $html);
		$html = preg_replace('#<p\s*/?>#i', "\n", $html);$html = preg_replace('#</p>#i', "\n", $html);
		$html = preg_replace('#<h1\s*/?>#i', "\n", $html);$html = preg_replace('#</h1>#i', "\n", $html);
		$html = preg_replace('#<h2\s*/?>#i', "\n", $html);$html = preg_replace('#</h2>#i', "\n", $html);
		$html = preg_replace('#<h3\s*/?>#i', "\n", $html);$html = preg_replace('#</h3>#i', "\n", $html);
		$html = preg_replace('#<div\s*/?>#i', "\n", $html);$html = preg_replace('#</div>#i', "\n", $html);
		
		$content1=strip_tags($html);
				
		$order = array("\r\n", "\n", "\r","&nbsp;");
		$replace = ' ';

		// Processes \r\n's first so they aren't converted twice.
		$content1 = str_replace($order, $replace, $content1);$content1 = preg_replace('/\s\s+/', ' ', $content1);
		$content1 = preg_replace("/[^ \w]+/", " ", $content1);
		$content1 = strtolower($content1);
		
		$search = array('/(\s)+/s');		// shorten multiple whitespace sequences
		$replace = array('\\1');
 		
		$content1 = preg_replace($search, $replace, $content1);
		$content1 = rtrim($content1, ' ');
				
		return $content1;
	}

	public function wordStem($word) {
	#PHP implementation of the Porter Stemming Algorithm
	#Written by Iain Argent for Complinet Ltd., 17/2/00
	#Translated from the PERL version at http://www.muscat.com/~martin/p.txt
	#Version 1.1 (Includes British English endings)
	#--Reduces words to their base stem for search engines and indexing
		$step2list=array(
		'ational'=>'ate', 'tional'=>'tion', 'enci'=>'ence', 'anci'=>'ance', 'izer'=>'ize',
		'iser'=>'ise', 'bli'=>'ble',
		'alli'=>'al', 'entli'=>'ent', 'eli'=>'e', 'ousli'=>'ous', 'ization'=>'ize',
		'isation'=>'ise', 'ation'=>'ate',
		'ator'=>'ate', 'alism'=>'al', 'iveness'=>'ive', 'fulness'=>'ful', 'ousness'=>'ous',
						'aliti'=>'al',
		'iviti'=>'ive', 'biliti'=>'ble', 'logi'=>'log'
		);

		$step3list=array(
		'icate'=>'ic', 'ative'=>'', 'alize'=>'al', 'alise'=>'al', 'iciti'=>'ic', 'ical'=>'ic',
		'ful'=>'', 'ness'=>''
		);

		$c = "[^aeiou]"; # consonant
		$v = "[aeiouy]"; # vowel
		$C = "${c}[^aeiouy]*"; # consonant sequence
		$V = "${v}[aeiou]*"; # vowel sequence

		$mgr0 = "^(${C})?${V}${C}"; # [C]VC... is m>0
		$meq1 = "^(${C})?${V}${C}(${V})?" . '$'; # [C]VC[V] is m=1
		$mgr1 = "^(${C})?${V}${C}${V}${C}"; # [C]VCVC... is m>1
		$_v = "^(${C})?${v}"; # vowel in stem

		if (strlen($word)<3) return $word;

				$word=preg_replace("/^y/", "Y", $word);

		#Step 1a
				$word=preg_replace("/(ss|i)es$/", "\\1", $word);        # sses-> ss, ies->es
				$word=preg_replace("/([^s])s$/", "\\1", $word);         #        ss->ss but s->null

		#Step 1b
				if (preg_match("/eed$/", $word)) {
						$stem=preg_replace("/eed$/", "", $word);
						if (ereg("$mgr0", $stem)) {
								$word=preg_replace("/.$/", "", $word);
						}
				}
				elseif (preg_match("/(ed|ing)$/", $word)) {
						$stem=preg_replace("/(ed|ing)$/", "", $word);
						if (preg_match("/$_v/", $stem)) {
								$word=$stem;

								if (preg_match("/(at|bl|iz|is)$/", $word)) {
										$word=preg_replace("/(at|bl|iz|is)$/", "\\1e", $word);
								}

								elseif (preg_match("/([^aeiouylsz])\\1$/", $word)) {
										$word=preg_replace("/.$/", "", $word);
								}

								elseif (preg_match("/^${C}${v}[^aeiouwxy]$/", $word)) {
										$word.="e";
								}
						}
				}

		#Step 1c (weird rule)
				if (preg_match("/y$/", $word)) {
						$stem=preg_replace("/y$/", "", $word);
						if (preg_match("/$_v/", $stem))
								$word=$stem."i";
				}

		#Step 2
				if
		(preg_match("/(ational|tional|enci|anci|izer|iser|bli|alli|entli|eli|ousli|ization|isation|ation|ator|alism|iveness|fulness|ousness|aliti|iviti|biliti|logi)$/",
		$word, $matches)) {
				
		$stem=preg_replace("/
		(ational|tional|enci|anci|izer|iser|bli|alli|entli|eli|ousli|ization|isation|ation|ator|alism|iveness|fulness|ousness|aliti|iviti|biliti|logi)$/",
		"", $word);
						$suffix=$matches[1];
						if (preg_match("/$mgr0/", $stem)) {
								$word=$stem.$step2list[$suffix];
						}
				}

		#Step 3
				if (preg_match("/(icate|ative|alize|alise|iciti|ical|ful|ness)$/", $word, $matches)) {
						$stem=preg_replace("/(icate|ative|alize|alise|iciti|ical|ful|ness)$/", "", $word);
						$suffix=$matches[1];
						if (preg_match("/$mgr0/", $stem)) {
								$word=$stem.$step3list[$suffix];
						}
				}

		#Step 4
				if
		(preg_match("/(al|ance|ence|er|ic|able|ible|ant|ement|ment|ent|ou|ism|ate|iti|ous|ive|ize|ise)$/",
		$word, $matches)) {
				
		$stem=preg_replace("/(al|ance|ence|er|ic|able|ible|ant|ement|ment|ent|ou|ism|ate|iti|ous|ive|ize|ise)$/",
		"", $word);
						$suffix=$matches[1];
						if (preg_match("/$mgr1/", $stem)) {
								$word=$stem;
						}
				}
				elseif (preg_match("/(s|t)ion$/", $word)) {
						$stem=preg_replace("/(s|t)ion$/", "\\1", $word);
						if (preg_match("/$mgr1/", $stem)) $word=$stem;
				}

		#Step 5
				if (preg_match("/e$/", $word, $matches)) {
						$stem=preg_replace("/e$/", "", $word);
						if (preg_match("/$mgr1/", $stem) |
								(preg_match("/$meq1/", $stem) &
								~preg_match("/^${C}${v}[^aeiouwxy]$/", $stem))) {
								$word=$stem;
						}
				}
				if (preg_match("/ll$/", $word) & preg_match("/$mgr1/", $word)) $word=preg_replace("/.$/", "",
		$word);

		# and turn initial Y back to y
				preg_replace("/^Y/", "y", $word);

				return $word;	
	}

	public function getLevenshtein($input,$words){
		//similar_text() might work too.
		// array of words to check against
		//$words  = array('apple','pineapple','banana','orange',						'radish','carrot','pea','bean','potato','comput','lactat');

		// no shortest distance found, yet
		$shortest = -1;

		// loop through words to find the closest
		foreach ($words as $word) {

			// calculate the distance between the input word,
			// and the current word
			$lev = levenshtein($input, $word);

			// check for an exact match
			if ($lev == 0) {

				// closest word is this one (exact match)
				$closest = $word;
				$shortest = 0;

				// break out of the loop; we've found an exact match
				break;
			}

			// if this distance is less than the next found shortest
			// distance, OR if a next shortest word has not yet been found
			if ($lev <= $shortest || $shortest < 0) {
				// set the closest match, and shortest distance
				$closest  = $word;
				$shortest = $lev;
			}
		}

		//echo "Input word: $input\n";
		if ($shortest == 0) {//0 is an exact match
		//	echo "perfect match found: $closest\n";
			return array('closest'=>$closest,'shortest'=>$shortest);
		} 
		if (strlen($input) < 4 && $shortest < 2) {//0 is an exact match
			//echo "match found 4: $closest\n";
			return array('closest'=>$closest,'shortest'=>$shortest);
		} 
		if (strlen($input) > 3 && $shortest < 3) {//0 is an exact match
			//echo "match found 4: $closest\n";
			return array('closest'=>$closest,'shortest'=>$shortest);
		} 
		if(strlen($input) > 6 && $shortest < 6){
		//echo "match found 6: $closest\n";
			return array('closest'=>$closest,'shortest'=>$shortest);
		}
		
		
	}
	
	//Process Search Input
	public function search($input=''){

		if($input != ''){
		
		
		$sql="SELECT * FROM searchindex";
		$stmt=$this->pdo->prepare($sql);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		foreach($rows as $row){
			$wordArray[] = $row['word'];
		}
		
		$searchArray = explode(' ',$input);
		
		foreach($searchArray as $word){
			if(!in_array($word,$this->stopwords)){
				//$stemmed[] = $this->wordStem($word);
				$sql="SELECT * FROM searchindex WHERE word =?";
				$stmt=$this->pdo->prepare($sql);
				$levens=$this->getLevenshtein($this->wordStem(strtolower($word)),$wordArray);
				$stmt->execute(array($levens['closest']));
				$row = $stmt->fetch(PDO::FETCH_ASSOC);
				if(!empty($row['pagelist'])){
					$matchedids[$row['word']]['page']=array('set'=>$row['pagelist'],'score'=>$levens['shortest']);	//maybe use levinshteins exact match, or not, for weighting	
				}
				if(!empty($row['articlelist'])){
					$matchedids[$row['word']]['article']=array('set'=>$row['articlelist'],'score'=>$levens['shortest']);	//maybe use levinshteins exact match, or not, for weighting	
				}
				$levens='';
			}		
		}
				//echo '<pre>',htmlspecialchars(print_r($matchedids, true)),'</pre>';
		//$results[]= ['id'=>$id,'type'=>$type,'score'=>$score];
		$uniqueids=array();
		foreach($matchedids as $word => $results){
		unset($score);
			$pageids = explode(',',$results['page']['set']);
			$articleids = explode(',',$results['article']['set']);
			if($results['page']['score'] === 0){
				$score['page']= 1;
			}else if($results['page']['score'] < 2 ){
				$score['page'] = .9;
			}else{
				$score['page'] = .7;
			}
			if($results['article']['score'] === 0){
				$score['article']= 1;
			}else if($results['article']['score'] < 2 ){
				$score['article'] = .9;
			}else{
				$score['article'] = .7;
			}	
		
			
			
			foreach($pageids as $id){
				if ($id !=''){
					if(!array_key_exists($id,$uniqueids['page'])){
						$uniqueids['page'][$id]=$score['page'];
					}else{
						$uniqueids['page'][$id]+=$score['page'];
					}			
				}
			}		

			foreach($articleids as $id){
				if ($id !=''){
					if(!array_key_exists($id,$uniqueids['article'])){
						$uniqueids['article'][$id]=$score['article'];
					}else{
						$uniqueids['article'][$id]+=$score['article'];
					}
				}
			}
		
		}
		
		$records=[];
		foreach($uniqueids as $type => $value){
			foreach($value as $id => $score){
			$records[]=['id'=>$id,'type'=>$type,'score'=>$score];
			}
		
		}
		
		
		function sortByOrder($a, $b) {
			return $a['score'] < $b['score'];
		}
		usort($records, 'sortByOrder');
		
			
		//echo '<pre>',htmlspecialchars(print_r($records, true)),'</pre>';

		
		return $records;
			
		}
	}
	

	function stripHTML($html){

		$html = preg_replace('~<\s*\bscript\b[^>]*>(.*?)<\s*\/\s*script\s*>~is', '', $html);//remove scripts
		$html = preg_replace('~<\s*\bstyle\b[^>]*>(.*?)<\s*\/\s*style\s*>~is', '', $html);
		$html = preg_replace('~<\s*\bnav\b[^>]*>(.*?)<\s*\/\s*nav\s*>~is', '', $html);
		$html = preg_replace('#<br\s*/?>#i', "\n", $html);
		$html = preg_replace('#<p\s*/?>#i', "\n", $html);$html = preg_replace('#</p>#i', "\n", $html);
		$html = preg_replace('#<h1\s*/?>#i', "\n", $html);$html = preg_replace('#</h1>#i', "\n", $html);
		$html = preg_replace('#<h2\s*/?>#i', "\n", $html);$html = preg_replace('#</h2>#i', "\n", $html);
		$html = preg_replace('#<h3\s*/?>#i', "\n", $html);$html = preg_replace('#</h3>#i', "\n", $html);
		$html = preg_replace('#<div\s*/?>#i', "\n", $html);$html = preg_replace('#</div>#i', "\n", $html);
		
		$content1=strip_tags($html);	
		$order = array("\r\n", "\n", "\r","&nbsp;");
		$replace = ' ';

		// Processes \r\n's first so they aren't converted twice.
		$content1 = str_replace($order, $replace, $content1);
		
				
		return $content1;
	}
}