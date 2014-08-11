<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Cms
 *
 * Simple tool for making simple sites.
 *
 * @package		Pusaka
 * @author		Toni Haryanto (@toharyan)
 * @copyright	Copyright (c) 2011-2012, Nyankod
 * @license		http://nyankod.com/license
 * @link		http://nyankod.com/pusakacms
 */
class CMS extends MY_Controller {

	/**
	 * Main CMS Function
	 *
	 * Routes and processes all page requests
	 *
	 * @access	public
	 * @return	void
	 */
	public function _remap($method, $params = array())
	{
		// run the main method first if available
		if (method_exists($this, $method))
			return call_user_func_array(array($this, $method), $params);

		$segments = $this->uri->segment_array();

		$is_home = FALSE;

		// Blank mean it's the home page, ya hurd?
		if (empty($segments))
		{
			$is_home = TRUE;
			$segments = array('index');
		}

		// reset index to 0
		$segments = array_values($segments);
		
		// if it is STREAM POST
		if($segments[0] == $this->config->item('post_term'))
		{
			return call_user_func_array(array($this, 'post'), $params);
		}
		// if it is a PAGE
		else 
		{
			$file_path = PAGE_FOLDER.'/'.implode('/', $segments);
			
			// check if there is a custom layout for this page
			if($this->template->layout_exists(implode("/",$segments)))
				$this->template->set_layout(implode("/",$segments));
			elseif($this->template->layout_exists($segments[0]))
				$this->template->set_layout($segments[0]);

			$this->template->view_content($file_path, $this->data);
		}
	}

	function sync_nav()
	{
		$this->pusaka->sync_nav();
	}

	function post()
	{
		$this->template->set_layout(null);

		// it is a post list
		if(! isset($segments[1])){
			$this->data['posts'] = $this->pusaka->get_posts();

			$this->template->view('layouts/posts', $this->data);
		}
		else {
			// if it is a blog label
			if($segments[1] == 'label'){
				if(! isset($segments[2])) show_404();

				$this->data['posts'] = $this->pusaka->get_posts($segments[2], $segments[3] ? $segments[3] : 1);
			} 
			
			// if it is a post list with page number
			elseif($segments[1] == 'p'){
				if(! isset($segments[2])) show_404();

				$this->data['posts'] = $this->pusaka->get_posts(null, $segments[3] ? $segments[3] : 1);
			}
			
			// if it is a label page
			else {
				$this->template->view('layouts/post');

				$this->data['posts'] = $this->pusaka->get_posts();
				
				$this->template->view('layouts/posts', $this->data);
			}
		}
	}

}