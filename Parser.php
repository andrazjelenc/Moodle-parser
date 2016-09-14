<?php

class Parser
{
	public $html = '';
	
	public function getMainLinks()
	{
		$links = array();
		
		$dom = new DOMDocument();
		$dom->loadHTML($this->html);

		$course = $this->getElementsByClass($dom, 'div', 'course-content');
		
		foreach ($course[0]->getElementsByTagName('a') as $node)
		{
			$links[] = array('value' => $node->nodeValue, 'link' => $node->getAttribute("href"));
		}
		unset($dom);
		return $links;
	}
	
	public function getNews()
	{
		$news = array();
		
		$dom = new DOMDocument();
		$dom->loadHTML($this->html);

		$bloackNewsItems = $this->getElementsByClass($dom, 'div', 'block_news_items');//$finder->evaluate("//*[contains(@class, 'block_news_items')]");

		$newsContainer = '';
		foreach($bloackNewsItems as $newsC)
		{
			$title = $this->getElementsByClass($newsC, 'div', 'title');
			if(count($title) == 1 && $title[0]->textContent == 'Zadnje novice')
			{
				//that's it;
				$newsContainer = $newsC;
				break;
			}
			
		}
		if($newsContainer == '')
		{
			return $news;
		}
			
		//we got out container!
		$infos = $this->getElementsByClass($newsContainer, 'div','info');
		$names = $this->getElementsByClass($newsContainer, 'div','name');
		$dates = $this->getElementsByClass($newsContainer, 'div','date');

		for($i = 0; $i < count($infos); $i++)
		{
			$date = $dates[$i]->textContent;
			$name = $names[$i]->textContent;
			$info = $infos[$i]->textContent;
			//echo $date."\r\n".$name."\r\n".$info."\r\n\r\n";
			$news[] = array('date' => $date, 'name' => $name, 'info' => $info);
		}
		unset($dom);
		return $news;

	}
	
	private function getElementsByClass(&$parentNode, $tagName, $className) 
	{
		$nodes=array();

		$childNodeList = $parentNode->getElementsByTagName($tagName);
		for ($i = 0; $i < $childNodeList->length; $i++) 
		{
			$temp = $childNodeList->item($i);
			if (stripos($temp->getAttribute('class'), $className) !== false) 
			{
				$nodes[]=$temp;
			}
		}
		return $nodes;
	}
}
?>
