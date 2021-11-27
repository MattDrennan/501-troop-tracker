// Include
const puppeteer = require('puppeteer');
const cheerio = require('cheerio');
var mysql = require('mysql');
const Sheets = require("node-sheets").default;
require('dotenv').config()

// URL variables
const url = "https://mandalorianmercs.org/forum/?action=login";
const url2 = "https://mandalorianmercs.org/forum/index.php?topic=136006.0";

// MySQL
var con = mysql.createConnection({
	host: process.env.MYSQL_HOST,
	user: process.env.MYSQL_USER,
	password: process.env.MYSQL_PASSWORD
});

con.connect(function(err) {
  if (err) throw err;
  console.log("Connected to MySQL!");
});

// Clean Mando Troopers
con.query("DELETE FROM " + process.env.MYSQL_TABLE + ".mando_troopers", function (err, result) {
	if (err) throw err;
	console.log("Result: " + result);
});

// Clean Mando Costumes
con.query("DELETE FROM " + process.env.MYSQL_TABLE + ".mando_costumes", function (err, result) {
	if (err) throw err;
	console.log("Result: " + result);
});

(async () => {
	// Launch browser
	const browser = await puppeteer.launch();
	const page = await browser.newPage();
	// Load URL
	await page.goto(url, { waitUntil: 'networkidle0' });
	await page.type('input[name=user]', process.env.USERNAME_FORUM);
	await page.type('input[name=passwrd]', process.env.PASSWORD_FORUM);
	await Promise.all([
		page.click('input[value=Login]'),
		page.waitForNavigation({ waitUntil: 'networkidle0' }),
	]);
	await page.goto(url2, { waitUntil: 'networkidle0' });
	
	let data = await page.evaluate(() => {
		const srcs = Array.from(document.querySelectorAll("#msg_1757143 img[class=bbc_img]")).map((image) => image.getAttribute("src"));
		return srcs;
	});
	
	let data2 = await page.evaluate(() => {
		const srcs = Array.from(document.querySelectorAll("#msg_1757143 span[class=bbc_size]")).map((image) => image.innerText);
		return srcs;
	});
	
	let data3 = await page.evaluate(() => {
		const srcs = Array.from(document.querySelectorAll("#msg_1757143 span[class=bbc_size]")).map((image) => image.innerHTML);
		return srcs;
	});
	
	// Convert to array by line break
	let mandos = data2.toString().split(/\r?\n/);
	
	// Remove top of Mandos (Text)
	mandos.splice(0, 5);
	
	// Filtered mando array
	const mandoArray = [];
	
	// Filtered image array
	const imageArray = [];
	
	// Filtered html array
	const htmlArray = [];
	
	// When to reset
	let resetCheck = 0;
	let tempHold = 0;
	let tempHold2 = 0;
	let lastHold = "";
	
	// Loop through mando data
	for (let i = 0; i < mandos.length; i++)
	{
		// Only add to new array what we want to keep
		if(!mandos[i].includes("BV") && !mandos[i].includes("Member Roster") && mandos[i].trim().length > 0)
		{
			mandoArray.push(mandos[i]);
			
			// Check if has cat#
			if(mandos[i].includes("cat#"))
			{
				// Loop through data
				for (let j = 0; j < data3.length; j++)
				{
					// If found
					if(data3[j].indexOf(mandos[i].trim()) > -1)
					{
						// Add to array
						if(resetCheck == 0)
						{
							// Set
							tempHold = data3[j].indexOf(mandos[i].trim());
							
							// Add to html array
							htmlArray.push(data3[j].slice(0, tempHold));
						}
						else
						{
							// Add to html array
							htmlArray.push(data3[j].slice(tempHold, data3[j].indexOf(mandos[i].trim())));
							
							// Set
							tempHold = data3[j].indexOf(mandos[i].trim());
						}
						
						// Increment
						resetCheck++;
					}
				}
			}
		}
	}
	
	// Loop through image data
	for (let i = 0; i < data.length; i++)
	{
		if(!data[i].includes("Themes/"))
		{
			imageArray.push(data[i]);
		}
	}
	
	// Loop through HTML array and load images
	for (let i = 1; i < htmlArray.length; i++)
	{
		// Set up
		var catN;
		var name;
		var costume;
		
		// For general
		const $ = cheerio.load(htmlArray[i]);
		
		// For costume names because it doesn't load first costume
		const $$ = cheerio.load(htmlArray[i - 1]);
		
		// Loop through costumes
		$$("span").each(function(){
			if($(this).css("font-size") == "18pt")
			{
				// Set
				costume = $(this).text();
				
				// Print
				console.log($(this).text());
			}
		});
		
		// Make sure it has cat #
		if(htmlArray[i].indexOf("cat#") > -1)
		{
			// Get cat number
			catN = /\(([^)]*)\)/.exec(htmlArray[i])[1];
			catN = catN.replace("cat#", "");
			console.log(catN);
		}
		
		// Get name
		name = htmlArray[i].split("-");
		name = name[0].trim();
		name = name.replace(/(<([^>]+)>)/ig,"");
		name = name.split("<")[0];
		
		// Loop through images
		$("img").each(function() {
			// Print
			console.log($(this).attr("src"));
			
			// Insert
			con.query("INSERT INTO " + process.env.MYSQL_TABLE + ".mando_costumes (mandoid, costumeurl) VALUES (" + catN + ", '" + $(this).attr("src") + "')", function (err, result) {
				if (err) throw err;
				console.log("Result: " + result);
			});
		});
		
		// Insert
		con.query("INSERT INTO " + process.env.MYSQL_TABLE + ".mando_troopers (mandoid, name, costume) VALUES (" + catN + ", '" + mysql_real_escape_string(name) + "', '" + mysql_real_escape_string(costume) + "')", function (err, result) {
			if (err) throw err;
			console.log("Result: " + result);
		});
		
		console.log(name);
		
		console.log("=====================================================");
	}
	
	// Get Google Sheets
	const gs = new Sheets("1ImwIUou5Chc0WyEV9C4Zc2UoP23xeS6Eai5s7GtQrWs");
	await gs.authorizeApiKey(process.env.GOOGLE_SHEET_KEY);
	const table = await gs.tables("Costumes");
	console.log(table.rows);
	
	// Loop through Google Sheets
	for(var i in table.rows)
	{
		// Insert
		con.query("INSERT INTO " + process.env.MYSQL_TABLE + ".mando_costumes (mandoid, costumeurl) VALUES (" + mysql_real_escape_string(table.rows[i]['ID']['value']) + ", '" + mysql_real_escape_string(table.rows[i]['Costume Image URL']['value']) + "')", function (err, result) {
			if (err) throw err;
			console.log("Result: " + result);
		});
	}
	
	const table2 = await gs.tables("Troopers");
	console.log(table2.rows);
	
	// Loop through Google Sheets
	for(var i in table2.rows)
	{
		// Insert
		con.query("INSERT INTO " + process.env.MYSQL_TABLE + ".mando_troopers (mandoid, name, costume) VALUES (" + mysql_real_escape_string(table2.rows[i]['ID']['value']) + ", '" + mysql_real_escape_string(table2.rows[i]['Name']['value']) + "', '" + mysql_real_escape_string(table2.rows[i]['Costume Name']['value']) + "')", function (err, result) {
			if (err) throw err;
			console.log("Result: " + result);
		});
	}
	
	// Output
	//console.dir(mandoArray, {'maxArrayLength': null})
	//console.dir(imageArray, {'maxArrayLength': null})
	//console.dir(data3, {'maxArrayLength': null})
	//console.dir(htmlArray, {'maxArrayLength': null})
})();

// mysql_real_escape_string: Escapes string to make MySQL safe
function mysql_real_escape_string(str)
{
	if (typeof str != 'string')
		return str;

	return str.replace(/[\0\x08\x09\x1a\n\r"'\\\%]/g, function (char)
	{
		switch (char) {
			case "\0":
			return "\\0";
			case "\x08":
			return "\\b";
			case "\x09":
			return "\\t";
			case "\x1a":
			return "\\z";
			case "\n":
			return "\\n";
			case "\r":
			return "\\r";
			case "\"":
			case "'":
			case "\\":
			case "%":
			return "\\"+char;
		}
	});
}