function detectDevice() {
    var containerScannerHidden = document.getElementById("hiddenScanner");
    if (window.innerWidth < 1000) {
        containerScannerHidden.style.display = "flex";
        console.log("i am on a phone");
    }
}

var ChromeSamples = {
    log: function () {
        var line = Array.prototype.slice.call(arguments).map(function (argument) {
            return typeof argument === 'string' ? argument : JSON.stringify(argument);
        }).join(' ');

        document.querySelector('#log').textContent += line + '\n';
    },

    clearLog: function () {
        document.querySelector('#log').textContent = '';
    },

    setStatus: function (status) {
        document.querySelector('#status').textContent = status;
    },

    setContent: function (newContent) {
        var content = document.querySelector('#content');
        while (content.hasChildNodes()) {
            content.removeChild(content.lastChild);
        }
        content.appendChild(newContent);
    }
};

// 
// 

btnScan.addEventListener("click", async () => {
    log("Scanning...");

    try {
        const ndef = new NDEFReader();
        await ndef.scan();

        ndef.addEventListener("readingerror", () => {
            log("Argh! Cannot read data from the NFC tag. Try another one?");
        });

        ndef.addEventListener("reading", ({ message, serialNumber }) => {
            searchStudent(serialNumber);
            log(`> Serial Number: ${serialNumber}`);
            log(`> Records: (${message.records.length})`);
        });
    }
    catch (error) {
        log("Argh! " + error);
    }
});

// 
// 

/* jshint ignore:start */
(function (i, s, o, g, r, a, m) {
    i['GoogleAnalyticsObject'] = r; i[r] = i[r] || function () {
        (i[r].q = i[r].q || []).push(arguments)
    }, i[r].l = 1 * new Date(); a = s.createElement(o),
        m = s.getElementsByTagName(o)[0]; a.async = 1; a.src = g; m.parentNode.insertBefore(a, m)
})(window, document, 'script', 'https://www.google-analytics.com/analytics.js', 'ga');
ga('create', 'UA-53563471-1', 'auto');
ga('send', 'pageview');
/* jshint ignore:end */


//CODE TO GET DATA FROM SQL SERVER
fetch()