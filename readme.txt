=== Plugin Name ===
Contributors: SiteGuarding
Donate link: https://www.siteguarding.com/en/website-extensions
Tags: antivirus, malware, virus, scanner, security, block, blocked, attack, hack, hacker, hacking, protection, website security, scan, malware removal, virus detection
Requires at least: 3.0
Tested up to: 3.9 
Stable tag: 2.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Adds more security for your website. Server-side scanning. Performs deep website scans of all the files. Virus/Malware detection and removal.

== Description ==

WP Antivirus Site Protection is the security plugin to prevent/detect and remove malicious computer viruses. 
It detects: backdoors, rootkits, trojan horses, worms, fraudtools, adware and spyware and etc.

**Main features:**

* Deep scan of every file on your website.
* Daily update of the virus database.
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
for suspicious codes in the files. Please note: This action requires your permission 
and confirmation (nothing will be downloaded and sent to SiteGuarding server without your permission). 

3) Scan process. During the scanning process, plugin will read all the files of your website and will analyze 
them. Information about the files with suspicious codes will be sent to SiteGuarding server for extra analyze 
and for report generation. Generated report will be sent back to you (the copy of the report you will get by 
email)   

== Installation ==

1. Upload all the files to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= Q: Will WP Antivirus Site Protection plugin slow my site down? =
A: Absolutely No.

= Q: How often do you update your virus database? =
A: When we find a new malware code, we analyze it and add the malware signature to our database. Once per day, sometimes 2-3 times per day.

= Q: My website is hacked, can you help to clean it and protect? =
A: Yes, we provide this kind of services. But it's not free. For more information please contact us (https://www.siteguarding.com/en/contacts)

= Q: WP Antivirus Site Protection did not find any infected files, but Heuristic Analyze says that some files can be infected. Can you review these file? =
A: Yes, we can do it. If you have PRO version of WP Antivirus Site Protection, it will be free of charge.

= Q: Something wrong with my website, but all scanners and monitors say that it's clean. Can you manually analyze the files of my website and fix the problem if it exists? =
A: Yes, we provide this kind of services. But it's not free. For more information please contact us (https://www.siteguarding.com/en/contacts)

= Q: WP Antivirus Site Protection found malware on my website. What should I do? =
A: If you have PRO version of WP Antivirus Site Protection, you can contact us and we will clean your website for free of charge. If you have basic version, you can try to clean your website by yourself. E.g. you can simple delete the file with malware code. BUT you need to be sure that it's not a part of your website. Sometimes hackers modify importan files of the website and hide their malware codes inside of these file. If you simple delete these files, your website will stop to work. We advice to get PRO version and our security experts will clean your website. 

== Screenshots ==

1. Antivirus scanner page.
2. Scanner process in progress.
3. Successful report (no suspicious codes in the files).
4. Scanner has found suspicious codes.

== Changelog ==

== Upgrade Notice ==
