=== Plugin Name ===
Contributors: SiteGuarding
Donate link: https://www.siteguarding.com/en/website-extensions
Tags: antivirus, malware, virus, scanner, security, block, blocked, attack, hack, hacker, hacking, protection, website security, scan, malware removal
Requires at least: 3.0
Tested up to: 3.9 
Stable tag: 1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Adds more security for your website. Server-side scanning. Performs deep website scans of all the files. Virus/Malware detection and removal.

== Description ==

WP Antivirus Site Protection is the security plugin to prevent/detect and remove malicious computer viruses. 
It detects: backdoors, rootkits, trojan horses, worms, fraudtools, adware and spyware and etc.

**Main features:**

* Deep scan of every file on your website.
* Alerts and Notifications in admin area and by email.
* Daily cron feature.
* Whitelist solution after manual review.
* Possibility to upload suspicious files to www.siteguarding.com server for review by experts.

Please note: Plugin sends and receives the data to SiteGuarding.com API.

**How it works:**

1) Registration. To communicate with SiteGuarding API, your website has to get session access key. 
Plugins sends information about your website (domain and email) to SiteGuarding server. After successful 
registration your website will get uniq access key. Please note: This action requires your permission 
and confirmation (nothing will be sent to SiteGuarding server without your permission).  

2) Preparation for scanning process. On this stage you need to confirm that you allow to scan and analyze
the files of your website. Note: Plugin DOES NOT delete or  modify the files. It just READS them and looking 
for suspicious codes in the files. Plugin downloads the latest version of scanner module from SiteGuarding. 
This module has the database of the signatures of suspicious codes and heuristic analysis functions to detect 
previously unknown viruses and malware codes. Please note: This action requires your permission 
and confirmation (nothing will be downloaded and sent to SiteGuarding server without your permission). 

3) Scan process. During the scanning process, plugin will read all the files of your website and will analyze 
them. Information about the files with suspicious codes will be sent to SiteGuarding server for extra analyze 
and for report generation. Generated report will be sent back to you (the copy of the report you will get by 
email)   

== Installation ==

1. Upload all the files to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

== Screenshots ==

1. Antivirus scanner page.
2. Successful report (no suspicious codes in the files).
3. Scanner has found suspicious codes.

== Changelog ==

== Upgrade Notice ==
