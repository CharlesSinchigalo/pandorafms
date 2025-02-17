# -*- coding: utf-8 -*-
from include.common_classes_60 import PandoraWebDriverTestCase
from include.common_functions_60 import login, is_element_present, click_menu_element, detect_and_pass_all_wizards, logout, gen_random_string, is_enterprise
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.common.keys import Keys
from selenium.webdriver.support.ui import Select
from selenium.common.exceptions import NoSuchElementException
from selenium.common.exceptions import NoAlertPresentException
from selenium.webdriver.remote.webelement import WebElement

import unittest2, time, re
import logging

class viewAppear(PandoraWebDriverTestCase):

	test_name = u'test menu'
	tickets_associated = []

	@is_enterprise
	def test_views_appear(self):

		u"""
		This test do login and check one by one that all views appear.
		"""
		
		"""	

		logging.basicConfig(filename="Views.log", level=logging.INFO, filemode='w')
		
		driver = self.driver
		self.login()
		detect_and_pass_all_wizards(driver)
		
		click_menu_element(driver,"Tactical view")
		time.sleep(2)
		self.assertEqual("Status report" in driver.page_source,True)
		click_menu_element(driver,"Group view")
		time.sleep(2)
		self.assertEqual("Summary of the status groups" in driver.page_source,True)
		time.sleep(2)
		click_menu_element(driver,"Tree view")
		time.sleep(2)
		self.assertEqual("Tree search" in driver.page_source,True)
		time.sleep(2)
		click_menu_element(driver,"Agent detail")
		time.sleep(2)
		self.assertEqual("Description" in driver.page_source,True)
		click_menu_element(driver,"Monitor detail")
		time.sleep(2)
		self.assertEqual("Monitor status" in driver.page_source,True)
		click_menu_element(driver,"Alert details")
		time.sleep(2)
		self.assertEqual("Alert control filter" in driver.page_source,True)
		click_menu_element(driver,"Agent/Alert view")	
		time.sleep(2)
		self.assertEqual("Agents / Alert templates" in driver.page_source,True)
		click_menu_element(driver,"Agent/Module view")
		click_menu_element(driver,"Module groups")
		click_menu_element(driver,"Real-time graphs")
		time.sleep(2)
		self.assertEqual("Clear graph" in driver.page_source,True)
		click_menu_element(driver,"Inventory")
		click_menu_element(driver,"Log viewer")
		time.sleep(2)
		self.assertEqual("Export to CSV" in driver.page_source,True)
		click_menu_element(driver,"SNMP console")
		click_menu_element(driver,"SNMP browser")
		time.sleep(2)
		self.assertEqual("Starting OID" in driver.page_source,True)
		click_menu_element(driver,"SNMP trap editor")
		time.sleep(2)
		self.assertEqual("Create" in driver.page_source,True)
		click_menu_element(driver,"MIB uploader")
		time.sleep(2)
		self.assertEqual("Index of attachment/mibs" in driver.page_source,True)
		click_menu_element(driver,"SNMP filters")
		click_menu_element(driver,"SNMP trap generator")		
		time.sleep(2)
		self.assertEqual("Host address" in driver.page_source,True)
		click_menu_element(driver,"Network map")
		time.sleep(2)
		self.assertEqual("There are no network maps defined yet" in driver.page_source,True)
		click_menu_element(driver,"Network console")
		click_menu_element(driver,"Services")
		click_menu_element(driver,"Visual console")
		click_menu_element(driver,"Custom reports")
		time.sleep(2)
		self.assertEqual("Create report" in driver.page_source,True)
		click_menu_element(driver,"Custom graphs")
		time.sleep(2)
		self.assertEqual("Total items" in driver.page_source,True)
		click_menu_element(driver,"Main dashboard")
		click_menu_element(driver,"Copy dashboard")
		time.sleep(2)
		self.assertEqual("Replicate Dashboard" in driver.page_source,True)
		click_menu_element(driver,"Custom SQL")
		time.sleep(2)
		self.assertEqual("Create custom SQL" in driver.page_source,True)
		click_menu_element(driver,"View events")
		time.sleep(2)
		self.assertEqual("Event control filter" in driver.page_source,True)
		click_menu_element(driver,"Statistics")
		click_menu_element(driver,"Edit my user")
		time.sleep(2)
		self.assertEqual("Password confirmation" in driver.page_source,True)
		click_menu_element(driver,"WebChat")
		time.sleep(2)
		self.assertEqual("Send message" in driver.page_source,True)
		click_menu_element(driver,"List of Incidents")
		click_menu_element(driver,"Statistics") 
		click_menu_element(driver,"Message list")
		time.sleep(2)
		self.assertEqual("Create message" in driver.page_source,True)
		click_menu_element(driver,"New message")
		click_menu_element(driver,"Connected users")
		time.sleep(2)
		click_menu_element(driver,"Export data")
		time.sleep(2)
		self.assertEqual("Source agent" in driver.page_source,True)
		click_menu_element(driver,"Scheduled downtime")
		time.sleep(2)
		self.assertEqual("Execution type" in driver.page_source,True)
		click_menu_element(driver,"Recon view")
		time.sleep(2)
		self.assertEqual("Task name" in driver.page_source,True)
		click_menu_element(driver,"File repository")
		click_menu_element(driver,"IPAM")
		time.sleep(2)
		self.assertEqual("IPAM" in driver.page_source,True)
		click_menu_element(driver,"Manage agents")
		time.sleep(2)
		self.assertEqual("Create agent" in driver.page_source,True)
		click_menu_element(driver,"Custom fields")
		time.sleep(2)
		self.assertEqual("Create field" in driver.page_source,True)
		click_menu_element(driver,"Component groups")
		time.sleep(2)
		self.assertEqual("Create" in driver.page_source,True)
		click_menu_element(driver,"Module categories")
		time.sleep(2)
		self.assertEqual("Create category" in driver.page_source,True)
		click_menu_element(driver,"Module types")
		click_menu_element(driver,"Module groups")
		click_menu_element(driver,"Insert Data")
		click_menu_element(driver,"Resource exporting")
		time.sleep(2)
		self.assertEqual("Export" in driver.page_source,True)
		click_menu_element(driver,"Resource registration")
		time.sleep(2)
		self.assertEqual("Upload" in driver.page_source,True)
		click_menu_element(driver,"Manage agent groups")
		time.sleep(2)
		self.assertEqual("Create group" in driver.page_source,True)
		click_menu_element(driver,"Module tags")
		time.sleep(2)
		self.assertEqual("Create tag" in driver.page_source,True)
		click_menu_element(driver,"Enterprise ACL Setup")
		time.sleep(2)
		self.assertEqual("Add" in driver.page_source,True)
		click_menu_element(driver,"Manage users")
		time.sleep(2)
		self.assertEqual("Create user" in driver.page_source,True)
		click_menu_element(driver,"Profile management")
		time.sleep(2)
		self.assertEqual("Create" in driver.page_source,True)
		click_menu_element(driver,"Connected users")
		time.sleep(2)	
		click_menu_element(driver,"Network components")
		time.sleep(2)
		self.assertEqual("Free Search" in driver.page_source,True)
		click_menu_element(driver,"Local components")
		time.sleep(2)
		self.assertEqual("Search" in driver.page_source,True)
		click_menu_element(driver,"Module templates")
		time.sleep(2)
		self.assertEqual("Create" in driver.page_source,True)
		click_menu_element(driver,"Inventory modules")
		click_menu_element(driver,"Manage policies")
		time.sleep(2)
		self.assertEqual("Create" in driver.page_source,True)
		click_menu_element(driver,"Collections")
		time.sleep(2)
		self.assertEqual("Create" in driver.page_source,True)
		click_menu_element(driver,"Duplicate config")
		time.sleep(2)
		self.assertEqual("Replicate configuration" in driver.page_source,True)
		click_menu_element(driver,"Agent operations")
		time.sleep(2)
		self.assertEqual("In order to perform massive operations" in driver.page_source,True)
		click_menu_element(driver,"Module operations")
		click_menu_element(driver,"Plugin operations")
		click_menu_element(driver,"User operations")
		time.sleep(2)
		click_menu_element(driver,"Alert operations")
		click_menu_element(driver,"Policies operations")
		click_menu_element(driver,"SNMP operations")
		click_menu_element(driver,"Satellite Operations")
		click_menu_element(driver,"List of Alerts")
		time.sleep(2)
		self.assertEqual("Alert control filter" in driver.page_source,True)
		click_menu_element(driver,"Templates")
		time.sleep(2)
		self.assertEqual("Create" in driver.page_source,True)
		click_menu_element(driver,"Actions")
		click_menu_element(driver,"Commands")
		click_menu_element(driver,"List of special days")
		click_menu_element(driver,"Event alerts")	
		click_menu_element(driver,"SNMP alerts")
		time.sleep(2)
		self.assertEqual("Maintenance" in driver.page_source,True)
		click_menu_element(driver,"Event filters")
		time.sleep(2)
		self.assertEqual("Create new filter" in driver.page_source,True)
		click_menu_element(driver,"Custom events")
		time.sleep(2)
		self.assertEqual("Update" in driver.page_source,True)
		click_menu_element(driver,"Event responses")
		time.sleep(2)
		self.assertEqual("Create response" in driver.page_source,True)
		click_menu_element(driver,"Manage servers")
		time.sleep(2)
		self.assertEqual("Saga" in driver.page_source,True)
		click_menu_element(driver,"Recon task")
		time.sleep(2)
		self.assertEqual("Create" in driver.page_source,True)
		click_menu_element(driver,"Plugins")
		time.sleep(2)
		self.assertEqual("Name" in driver.page_source,True)
		click_menu_element(driver,"Recon script")
		click_menu_element(driver,"Export targets")
		time.sleep(2)
		self.assertEqual("Create" in driver.page_source,True)
		click_menu_element(driver,"Register Plugin")
		time.sleep(2)
		self.assertEqual("Upload" in driver.page_source,True)
		click_menu_element(driver,"Cron jobs")
		time.sleep(2)
		self.assertEqual("Create" in driver.page_source,True)
		click_menu_element(driver,"General Setup")
		time.sleep(2)
		self.assertEqual("Pandora FMS Language settings" in driver.page_source,True)
		click_menu_element(driver,"Password policy")
		click_menu_element(driver,"Enterprise")
		click_menu_element(driver,"Historical database")
		click_menu_element(driver,"Log Collector")
		time.sleep(2)
		click_menu_element(driver,"Authentication")
		click_menu_element(driver,"Performance")
		click_menu_element(driver,"Visual styles")
		time.sleep(2)
		self.assertEqual("Behaviour configuration" in driver.page_source,True)
		click_menu_element(driver,"eHorus")
		time.sleep(2)
		self.assertEqual("Enable eHorus" in driver.page_source,True)
		click_menu_element(driver,"Edit OS")
		click_menu_element(driver,"Licence")
		time.sleep(2)
		self.assertEqual("Request new licence" in driver.page_source,True)
		click_menu_element(driver,"Skins")
		click_menu_element(driver,"Translate string")
		time.sleep(2)
		self.assertEqual("Search" in driver.page_source,True)
		click_menu_element(driver,"System audit log")
		time.sleep(2)
		self.assertEqual("User" in driver.page_source,True)
		click_menu_element(driver,"Links")
		time.sleep(2)
		self.assertEqual("Link name" in driver.page_source,True)
		click_menu_element(driver,"Diagnostic info")
		click_menu_element(driver,"Site news")
		time.sleep(2)
		self.assertEqual("Subject" in driver.page_source,True)
		click_menu_element(driver,"File manager")
		time.sleep(2)
		self.assertEqual("Index of images" in driver.page_source,True)
		click_menu_element(driver,"DB information")
		time.sleep(2)
		self.assertEqual("Module data received" in driver.page_source,True)
		click_menu_element(driver,"Database purge")
		click_menu_element(driver,"Database debug")
		time.sleep(2)
		click_menu_element(driver,"Database audit")
		click_menu_element(driver,"Database events")
		click_menu_element(driver,"DB Status")
		time.sleep(2)
		self.assertEqual("DB settings" in driver.page_source,True)
		click_menu_element(driver,"DB interface")
		time.sleep(2)
		self.assertEqual("Run SQL query" in driver.page_source,True)
		click_menu_element(driver,"API checker")
		time.sleep(2)
		self.assertEqual("IP" in driver.page_source,True)
		click_menu_element(driver,"System Info")
		time.sleep(2)
		self.assertEqual("Generate file" in driver.page_source,True)
		click_menu_element(driver,"Extension uploader")
		click_menu_element(driver,"File repository manager")	
		time.sleep(2)
		self.assertEqual("Groups" in driver.page_source,True)
		click_menu_element(driver,"System logfiles")
		click_menu_element(driver,"Backup")
		time.sleep(2)
		self.assertEqual("Description" in driver.page_source,True)
		click_menu_element(driver,"CSV import")
		time.sleep(2)
		self.assertEqual("Upload file" in driver.page_source,True)
		click_menu_element(driver,"Import groups with CSV file")
		time.sleep(2)
		self.assertEqual("Upload file" in driver.page_source,True)
		click_menu_element(driver,"IPAM")
		time.sleep(2)
		self.assertEqual("Create" in driver.page_source,True)
		click_menu_element(driver,"Update Manager offline")
		click_menu_element(driver,"Update Manager online")
		time.sleep(2)
		self.assertEqual("The last version of package installed is:" in driver.page_source,True)
		click_menu_element(driver,"Update Manager options")
		
		logging.info("test_views_appear is correct")

		"""

if __name__ == "__main__":
	unittest2.main()
