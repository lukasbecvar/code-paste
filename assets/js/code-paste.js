/* code highlight functionality */
import hljs from 'highlight.js'

// init code highligh
document.addEventListener('DOMContentLoaded', function() {
    hljs.highlightAll()
})

// copy button functionality
document.getElementById('copy-button').addEventListener('click', function () {
    var content = document.getElementById('content').textContent.trim();
    navigator.clipboard.writeText(content).then(function () {
    }).catch(function (error) {
        console.log('Copy failed: ' + error)
    })
})

// show Raw button functionality
document.getElementById('raw-button').addEventListener('click', function () {
    var urlParams = new URLSearchParams(window.location.search)
    var pasteFile = urlParams.get('f')
    window.location.href = '/raw?f=' + pasteFile
})
