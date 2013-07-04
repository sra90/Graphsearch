<?php
  extract($_GET);
	
	class Person
	{
        public $name;
		public $next;             // Holds the mygraph of pointers to friends
		public $friends; 			//Holds total count of friends
 
    
		function __construct($name)
		{
			$this->name = $name;
			$this->next = array();
			$this->friends = 0;
		}
 
		function readNode()
		{
			return $this->name;
		}

		function getTotalFriends()
		{
			return $this->friends;
		}

		function addFriend($n)
		{
			$this->next[$this->friends] = $n;
			$this->friends++;	
		}	
	
		function getFriend()
		{
			return $this->friends;
		}

		function getFriendNodes()
		{
			return $this->next;
		}
	}

	class Graph
	{
		public $table;     // Holds the pointers to nodes in the graph
		public $fullpath;   // Used to save the path from one node to the other in the "findPathTo" function
     
		function __construct()
		{       
			$this->table = array();			
			$this->fullpath = "";
		}
  
		public function insert($name)
		{
			$tmp = new Person($name);
			$this->table[$name] = $tmp;	
		}

		public function connect($n1,$n2)
		{
			$this->table[$n1]->addFriend($this->table[$n2]);
			$this->table[$n2]->addFriend($this->table[$n1]);		
		}

		public function findPathTo($start,$find,$visited,$path)
		{	
			if (!in_array($start,$visited)) 
			{		
				array_push($visited,$start);
				array_push($path,$start);		
				$neigh = $this->table[$start]->getFriendNodes();
				foreach ($neigh as $neighbour) 
				{
                    if (!in_array($neighbour->readNode(),$visited)) 
					{				
						if($find==$neighbour->readNode())
						{			
							foreach ($path as $op) 
							{
								$this->fullpath = $this->fullpath.",".$op;
							}
							$this->fullpath = $this->fullpath.";";			
						}
						$dummy = $this->findPathTo($neighbour->readNode(), $find,$visited,$path);
					}
				}		
				array_pop($path);
			}
		} 
   }
	
	$mygraph = new Graph();
	
	// Load all nodes
	$file = fopen("mynodes.txt", "r");
	$line = fgets($file);
	$allnodes = preg_split("/,/", $line);
	$allmynodes = "";
	foreach ($allnodes as $n) 
	{
		$mygraph->insert($n);
		$allmynodes = $allmynodes.$n.",";
	}
	fclose($file);
 
	// Load all connections
	$file = fopen("myfriends.txt", "r");
	$line = fgets($file);
	$fields = preg_split("/;/", $line);
	$allconnections = "";
	foreach ($fields as $n) 
	{
		$s = preg_split("/,/", $n);
		$allconnections = $allconnections.$s[0].",".$s[1].";";
		$mygraph->connect($s[0],$s[1]);
	}
 
	fclose($file);

	// Search for a path(s) if nodes exist
	if((isset($srch))&&(isset($srch2)))
	{
		if(in_array($srch,$allnodes)&&in_array($srch2,$allnodes))
		{
			$visited = array();
			$path = array();
			$route = array();
			$mygraph->fullpath="";
			$mygraph->findPathTo($srch,$srch2,$visited,$path);
			print_r($mygraph->fullpath);
		}
		else
		{
			echo "Node(s) not present.";
		}
	}
	
	// Add a person to the graph if not already present
	if(isset($person))
	{
		if(!in_array($person,$allnodes))
		{
			$file2 = fopen("mynodes.txt", "a");
			fwrite($file2, ",".$person);
			fclose($file2);
			echo "Added Successfully";
		}
		else
			echo "Already Present";
	}
	
	// Add a connection if nodes are present and are not connected
	if(isset($conn1)&&isset($conn2))
	{
		if(!in_array($conn1.",".$conn2,$fields))
		{
			$file2 = fopen("myfriends.txt", "a");
			fwrite($file2, ";".$conn1.",".$conn2);
			fclose($file2);
			$res = "Connected ".$conn1." and ".$conn2;
			echo $res;
		}
		else
			echo "Already Connected";
	}
	
	// To view all connections
	if(isset($conns))
	{
		echo $allconnections;
	}
	// To view all nodes
	if(isset($nodes))
	{
		echo $allmynodes;
	}
	// Save number of page hits
	if((!isset($conn1))&&(!isset($conn2))&&(!isset($person))&&(!isset($srch))&&(!isset($srch2)))
	{
		$file2 = fopen("pagehits.txt", "r");
		$line = fgets($file2);
		$line = $line + 0;
		$line++;
		fclose($file2);
		$file2 = fopen("pagehits.txt", "w");
		fwrite($file2,$line);
		fclose($file2);
		echo $line;
	}
?>




