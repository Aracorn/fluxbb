<?php

// Moved usage of pun_htmlspecialcharacters() to this template file. Better to go in viewtopic.php?

function viewtopic_template($cur_topic, $cur_user, $posts = array()) {
	// Fine using globals here?
	global $lang_common, $lang_topic;
	
	// Get current topic id from array
	$id = $cur_topic['id'];
	
	// Create reply button and/or "topic closed" text
	$post_link = '';
	
	if ($cur_user['can_post'])
		$post_link = '<a href="post.php?tid='.$id.'">'.$lang_topic['Post reply'].'</a>';
	
	if ($cur_topic['closed'] == '1')
	{
		if ($post_link)
			$post_link = $lang_topic['Topic closed'] . ' / ' . $post_link;
		else
			$post_link = $lang_topic['Topic closed'];
	}
	
	if ($post_link)
		$post_link = "\t\t\t".'<p class="postlink conr">'.$post_link.'</p>'."\n";
	
	// Create page links
	$paging_links = '<span class="pages-label">'.$lang_common['Pages'].' </span>'.paginate($cur_user['num_pages'], $cur_user['p'], 'viewtopic.php?id='.$id);

	// Create subscription links
	if ($cur_user['can_subscribe'])
	{
		if ($cur_topic['is_subscribed'])
			// I apologize for the variable naming here. It's a mix of subscription and action I guess :-)
			$subscraction = "\t\t".'<p class="subscribelink clearb"><span>'.$lang_topic['Is subscribed'].' - </span><a href="misc.php?action=unsubscribe&amp;tid='.$id.'">'.$lang_topic['Unsubscribe'].'</a></p>'."\n";
		else
			$subscraction = "\t\t".'<p class="subscribelink clearb"><a href="misc.php?action=subscribe&amp;tid='.$id.'">'.$lang_topic['Subscribe'].'</a></p>'."\n";
	}
	else
		$subscraction = '';
	
	
	// Output top section HTML
	?>
	<div class="linkst">
		<div class="inbox crumbsplus">
			<ul class="crumbs">
				<li><a href="index.php"><?php echo $lang_common['Index'] ?></a></li>
				<li><span>»&#160;</span><a href="viewforum.php?id=<?php echo $cur_topic['forum_id'] ?>"><?php echo pun_htmlspecialchars($cur_topic['forum_name']) ?></a></li>
				<li><span>»&#160;</span><strong><a href="viewtopic.php?id=<?php echo $id ?>"><?php echo pun_htmlspecialchars($cur_topic['subject']) ?></a></strong></li>
			</ul>
			<div class="pagepost">
				<p class="pagelink conl"><?php echo $paging_links ?></p>
	<?php echo $post_link ?>
			</div>
			<div class="clearer"></div>
		</div>
	</div>

	<?php
	
	$post_count = 0;
	
	// Output post HTML by looping through $posts array
	foreach ($posts as $cur_post)
	{
		$post_count++;
		
		// Add profile link to poster name where applicable
		if ($cur_post['show_user_link'])
			$username = '<a href="profile.php?id='.$cur_post['poster_id'].'">'.pun_htmlspecialchars($cur_post['username']).'</a>';
		else
			$username = pun_htmlspecialchars($cur_post['username']);
			
		// Format the online indicator. Require explicitly set true or false value in order to show.
		if ($cur_post['is_online'] === true) {
			$is_online = '<strong>'.$lang_topic['Online'].'</strong>';
		}
		elseif ($cur_post['is_online'] === false) {
			$is_online = '<span>'.$lang_topic['Offline'].'</span>';
		}
		else {
			$is_online = '';
		}
		
		// Set CSS classes for post container
		$post_class = 'blockpost';
		$post_class .= ($post_count % 2 == 0) ? ' roweven' : ' rowodd';
		if ($cur_post['is_first_post'])
			$post_class .= ' firstpost';
		if ($post_count == 1)
			$post_class .= ' blockpost1';

		?>
		<div id="p<?php echo $cur_post['id'] ?>" class="<?php echo $post_class ?>">
			<h2><span><span class="conr">#<?php echo $cur_post['post_num'] ?></span> <a href="viewtopic.php?pid=<?php echo $cur_post['id'].'#p'.$cur_post['id'] ?>"><?php echo format_time($cur_post['posted']) ?></a></span></h2>
			<div class="box">
				<div class="inbox">
					<div class="postbody">
						<div class="postleft">
							<dl>
								<dt><strong><?php echo $username ?></strong></dt>
								<dd class="usertitle"><strong><?php echo pun_htmlspecialchars($cur_post['user_title']) ?></strong></dd>
		<?php
		if ($cur_post['user_avatar'] != '')
			echo "\t\t\t\t\t\t".'<dd class="postavatar">'.$cur_post['user_avatar'].'</dd>'."\n";

		if (count($cur_post['user_info']))
		{
			foreach ($cur_post['user_info'] as $class => $info)
			{
				echo "\t\t\t\t\t\t\t\t".'<dd><span class="'.$class.'">'.pun_htmlspecialchars($info)."</span></dd>\n";
			}
		}
		
		// User_admin and user_contacts sub-arrays have text labels only so shouldn't need pun_htmlspecialchars
		// Exception is user_admin['admin_note'], which is escaped in viewtopic.php
		if (count($cur_post['user_admin']))
		{
			foreach ($cur_post['user_admin'] as $class => $link)
			{
				echo "\t\t\t\t\t\t\t\t".'<dd><span class="'.$class.'">'.$link."</span></dd>\n";
			}
		}

		if (count($cur_post['user_contacts'])) 
		{
			echo "\t\t\t\t\t\t\t\t".'<dd class="usercontacts">';
			
			foreach ($cur_post['user_contacts'] as $class => $link)
			{
				echo '<span class="'.$class.'">'.$link.'</span> ';
			}
			
			echo "</dd>\n";
		}
		?>
							</dl>
						</div>
						<div class="postright">
							<h3><?php if (!$cur_post['is_first_post']) echo $lang_topic['Re'].' '; ?><?php echo pun_htmlspecialchars($cur_topic['subject']) ?></h3>
							<div class="postmsg">
								<?php echo $cur_post['message']."\n" ?>
		<?php if ($cur_post['edited'] != '') echo "\t\t\t\t\t\t".'<p class="postedit"><em>'.$lang_topic['Last edit'].' '.pun_htmlspecialchars($cur_post['edited_by']).' ('.format_time($cur_post['edited']).')</em></p>'."\n"; ?>
							</div>
		<?php if ($cur_post['signature'] != '') echo "\t\t\t\t\t".'<div class="postsignature postmsg"><hr />'.$cur_post['signature'].'</div>'."\n"; ?>
						</div>
					</div>
				</div>
				<div class="inbox">
					<div class="postfoot clearb">
						<div class="postfootleft"><?php if ($cur_post['poster_id'] > 1) echo '<p>'.$is_online.'</p>'; ?></div>
		<?php if (count($cur_post['post_actions'])) echo "\t\t\t\t".'<div class="postfootright">'."\n\t\t\t\t\t".'<ul>'."\n\t\t\t\t\t\t".implode("\n\t\t\t\t\t\t", $cur_post['post_actions'])."\n\t\t\t\t\t".'</ul>'."\n\t\t\t\t".'</div>'."\n" ?>
					</div>
				</div>
			</div>
		</div>
	<?php
	} // End posts section
	
	// Output bottom links section HTML
	?>	
	<div class="postlinksb">
		<div class="inbox crumbsplus">
			<div class="pagepost">
				<p class="pagelink conl"><?php echo $paging_links ?></p>
	<?php echo $post_link ?>
			</div>
			<ul class="crumbs">
				<li><a href="index.php"><?php echo $lang_common['Index'] ?></a></li>
				<li><span>»&#160;</span><a href="viewforum.php?id=<?php echo $cur_topic['forum_id'] ?>"><?php echo pun_htmlspecialchars($cur_topic['forum_name']) ?></a></li>
				<li><span>»&#160;</span><strong><a href="viewtopic.php?id=<?php echo $id ?>"><?php echo pun_htmlspecialchars($cur_topic['subject']) ?></a></strong></li>
			</ul>
	<?php echo $subscraction ?>
			<div class="clearer"></div>
		</div>
	</div>

<?php
} // End function