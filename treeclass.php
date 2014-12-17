<?php
	class treenode{
		public $m_postid;
		public $m_title;
		public $m_poster;
		public $m_posted;
		public $m_children;
		public $m_childlist;
		public $m_depth;
		public $m_message;
		public $m_parent;
		
		public function __construct($postid, $title, $poster, $posted, $children, $expand,
									$depth, $expanded, $sublist, $message, $parent){
				$this->m_postid = $postid;
				$this->m_title = $title;
				$this->m_poster = $poster;
				$this->m_posted = $posted;
				$this->m_children = $children;
				$this->m_childlist = array();
				$this->m_depth = $depth;
				$this->m_message = $message;
				$this->m_parent = $parent;
			
					$conn = db_connect();
					
					$query = "select * from posts where parent = $postid order by posted";
					$result = $conn->query($query);
					
					while($row = @$result->fetch_assoc()){
		
						$this->m_childlist[] = new treenode($row['postid'], $row['title'], $row['poster'],
													 $row['posted'], $row['children'], $expand, $depth+1, $expanded,
													 $sublist, $row['message'], $row['parent']); 
					}
			}
		
		function display($row, $sublist=false){
				if($this->m_depth > -1){
					echo '<tr><td bgcolor= ';
					if($this->m_parent == 0){
						echo "'#cccccc'>";
					} else {
						echo "'#ffffff'>";
					}
					echo '<div class="border">';
					for($i=0; $i<$this->m_depth; $i++){
						echo "<img style='float: left; height: 200px; width: 200px;' src='images/spacer.gif' height=22 width=22 alt='' valign='bottom'>";
					}
					
					echo "<form action='view_post.php' method='post'><p>
					$this->m_title - $this->m_poster - ".$this->m_posted.'</p>';
					echo "<p>".$this->m_message.'</p>';
					echo '<p>Reply</p>';
					echo '<textarea name="reply_message"></textarea>';
					echo '<input type="hidden" name="id" value="'.$this->m_postid.'">';
					echo '<input type="submit" name="reply" value="Reply">';
					echo '</form>';
					echo '</td></tr>';
					echo '</div>';
					$row++;
				}
				
				$num_children = count($this->m_childlist);
				for($i=0; $i<$num_children; $i++){
					$row = $this->m_childlist[$i]->display($row, $sublist);
				}
				return $row;
			}
	}
?>


















































