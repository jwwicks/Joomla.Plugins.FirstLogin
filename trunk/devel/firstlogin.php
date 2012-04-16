<?php
/**
 * @version $Id$
 * @file 		firstlogin.php
 * @category	Plugin
 * @package		System
 * @subpackage  FirstLogin
 * @copyright 	Copyright (c) 2012 John William Wicks - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 *
 * @author  	John 'Ghost' Wicks
 * 
 */ 
defined('_JEXEC') or die;
jimport( 'joomla.plugin.plugin' );

class plgSystemFirstLogin extends JPlugin
{
	var $app;
	var $db;
	var $curUser;
	
	/**
	 * Constructor
	 *
	 * For php4 compatibility not using the __constructor
	 */
	function plgSystemFirstLogin(&$subject, $config){
		parent::__construct($subject, $config);
	}
	
	/**
	 * onUserLogin - Method is called when user logs in after authentication
	 *
	 * @param 	user		array holding user login data
	 * @param 	options		
	 */
	function onUserLogin($user, $options){
		$retVal = true;
					
		$this->app = &JFactory::getApplication();
		
		if(!$this->app->isAdmin()){
			$this->db = &JFactory::getDbo();
			
			$w1 = $this->db->quoteName("username")."=".$this->db->quote($user["username"]);
			
			$query = $this->db->getQuery(true);
			$query->select('id');
			$query->from('#__users');
			$query->where($w1);
			$this->db->setQuery((string)$query);
			$result = $this->db->loadResult();
			
			if($result){
				$this->curUser = &JFactory::getUser($result);
			}
	
			if(!$this->curUser->guest && $this->curUser->lastvisitDate == "0000-00-00 00:00:00")
			{ 
				$this->app->setUserState("firstlogin", "route1");
				$this->curUser->setLastVisit();
			}
		}
		
		return $retVal;
	}
	
	function onAfterRoute(){
		$this->app = &JFactory::getApplication();
		
		if(!$this->app->isAdmin()){
			if($this->app->getUserState("firstlogin")){
				$route = $this->params->get('route1');
				$this->app->setUserState("firstlogin", 0);
				$this->app->redirect($route);
			}
		}
	}
}