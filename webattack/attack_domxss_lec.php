<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Understanding DOM-Based XSS: An Overview</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #f4f4f9;
            color: #333;
            line-height: 1.6;
            padding: 20px;
        }

        h1, h2 {
            color: #007BFF;
        }

        h1 {
            font-size: 2rem;
            margin-bottom: 20px;
        }

        h2 {
            font-size: 1.5rem;
            margin-top: 20px;
        }

        p {
            margin: 10px 0;
        }

        ul, ol {
            margin: 10px 0 10px 20px;
        }

        ul li, ol li {
            margin: 5px 0;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }

        .navigation {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .nav-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px 20px;
            font-size: 1rem;
            font-weight: 600;
            color: #fff;
            background-color: #007BFF;
            text-decoration: none;
            border-radius: 5px;
            box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.2);
            transition: background-color 0.3s ease;
        }

        .nav-btn:hover {
            background-color: #0056b3;
        }

        .nav-btn span {
            margin: 0 5px;
        }

        .arrow {
            font-size: 1.2rem;
            font-weight: bold;
        }

        .arrow-back {
            margin-right: 10px;
        }

        .arrow-next {
            margin-left: 10px;
        }

        code {
            background-color: #f4f4f9;
            padding: 5px;
            border-radius: 4px;
            font-family: monospace;
            display: block;
            margin: 10px 0;
        }

        img {
            max-width: 100%;
            height: auto;
            margin: 10px 0;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Understanding DOM-Based XSS: An Overview</h1>
        <p>
            If you check any updated Security Advisories, it is easy to find new reflected and stored XSS vulnerabilities discovered monthly. However, the same is not true for DOM-based XSS, which is getting scarce nowadays. The reason is that DOM-based XSS is completely browser-based and does not need to go to the server and back to the client. At one point, a proof of concept DOM-based XSS could be created using a static HTML page; however, with the improved inherent security of web browsers, DOM-based XSS has become extremely difficult to exploit.
        </p>

        <h2>What is the Document Object Model (DOM)?</h2>
        <p>
            The DOM is a programming interface representing a web document as a tree. The DOM makes it possible to programmatically access and manipulate the different parts of a website using JavaScript. Let's consider a practical example.
        </p>
        <img src="pic dom1.png" alt="DOM Tree Example">
        <p>
            The DOM tree shown above is like the following list with sublists:
        </p>
        <ul>
            <li>The tree starts with the <code>document</code> node and branches into <code>DOCTYPE</code> and <code>html</code>.</li>
            <li>The <code>html</code> node branches into <code>head</code> and <code>body</code>.</li>
            <li>The <code>head</code> has the <code>title</code>, a few <code>meta</code> tags, and a <code>style</code>.</li>
            <li>In this simple example, the <code>body</code> has a single <code>div</code> that branches into one <code>h1</code> and two <code>p</code> elements.</li>
        </ul>

        <h2>Manipulating the DOM with JavaScript</h2>
        <p>
            We can view the DOM tree using the web browser's built-in Web Developer's Tools. For example, press <code>Ctrl + Shift + I</code> on Firefox and check the Inspector tab. Alternatively, we can access the JavaScript console. Using JavaScript, you can manipulate the DOM tree. For example, you can create a new element using <code>document.createElement()</code> and add a child to any element using <code>element.append()</code>.
        </p>
        <img src="dom 2.png" alt="DOM Manipulation Example">
        <p>
        In the example code above, we created two elements, div and p. Then, we appended the latter element to the div element.
        </p>

        <h2>Vulnerable Web Applications</h2>
        <p>
            DOM-based XSS vulnerabilities take place within the browser. They don't need to go to the server and return to the client's web browser. In other words, the attacker will try to exploit this situation by injecting a malicious script, for example, into the URL, and it will be executed on the client's side without any role for the server in this.
        </p>

        <h3>Vulnerable "Static" Site</h3>
        <img src="dom3.png" alt="DOM Tree Example">
        <p>
            The page below expects the user to provide their name after <code>?name=</code> in the URL. In the screenshot:
        </p>
        <ol>
            <li>The user has entered "Web Tester" after <code>?name</code> in the URL.</li>
            <li>The greeting worked as expected and displayed "Hello, Web Tester".</li>
            <li>The DOM structure on the right is left intact; the <code>&lt;body&gt;</code> has three direct children.</li>
        </ol>
        <img src="dom4.png" alt="Vulnerable Static Site Example">

        <p>
            The user might try to inject a malicious script. In the screenshot below:
        </p>
        <ol>
            <li>The user added <code>&lt;script&gt;alert("XSS")&lt;/script&gt;</code> instead of only "Web Tester" as their name.</li>
            <li>The script was executed, and an alert dialogue box was displayed.</li>
            <li>The DOM tree got a new element; the <code>&lt;body&gt;</code> has four children now.</li>
        </ol>
        <img src="dom5.png" alt="DOM-Based XSS Exploit Example">

        <h3>Fixed "Static" Site</h3>
        <img src="dom6.png" alt="DOM Tree Example">
        <p>
            One way to fix this page is by avoiding adding user input directly with <code>document.write()</code>. Instead, we first escape the user input using <code>encodeURIComponent()</code> and then add it to <code>textContent</code>.
        </p>
        <p>
            The previous attempt does not work now. We can see that:
        </p>
        <ol>
            <li>The user has added JavaScript as part of their input.</li>
            <li>The JavaScript code is displayed as encoded characters and presents no threat in the current context.</li>
            <li>The DOM structure is no longer affected when the user attempts to add code as part of their submitted name.</li>
        </ol>
        <img src="dom7.png" alt="Fixed Static Site Example">

        <h2>Conclusion</h2>
        <p>
            DOM-based XSS is a serious security vulnerability that occurs entirely within the browser. By avoiding insecure DOM manipulation methods like <code>document.write()</code> and properly escaping user input, developers can significantly reduce the risk of DOM-based XSS attacks. Always ensure that user-supplied data is properly sanitized before being used in DOM operations.
        </p>

        <div class="navigation">
            <a href="attack_domxss.php" class="nav-btn">
                <span class="arrow arrow-back">&larr;</span>
                <span>Back</span>
            </a>
            <a href="attack_domxss_lab.php" class="nav-btn">
                <span>Next</span>
                <span class="arrow arrow-next">&rarr;</span>
            </a>
        </div>
    </div>
</body>
</html>
