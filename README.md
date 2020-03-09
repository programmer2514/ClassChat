# ClassChat v0.4.9b
A PHP, AJAX, jQuery, and Javascript based web chat app
Try it out [here](https://classchat.calclover2514.repl.co/ "ClassChat v0.4.9")

---

Current features include:
- Creating new chat rooms
- Managing chat rooms
- Generating room links
- Sending images and formatted posts (Uses HTML)
- Voice-to-text
- Swear word filtering
- Individual Admin accounts with custom passwords
- Randomly generated user ID colors seeded with the user's ID
- Endless semi-public chat rooms
- User join notifications
- Message send dates

To be added:
- Deleting chat rooms from the server

---

**Adapting for new domains:**

If you would like to have this app run on your PHP server, only one thing is required:
1) Open `chat.php` in a code editor such as Notepad++
2) On lines 35-38, you should find the following code:
```
function generateLink() {
    $('#name-area a').attr('href', 'javascript:copyToClipboard("https://classchat.calclover2514.repl.co/room.php?room=' + chatroom + '")')
    $('#name-area a').html('https://classchat.calclover2514.repl.co/room.php?room=' + chatroom)
}
```
3) Replace `https://classchat.calclover2514.repl.co/room.php` with `https://your.website.domain/room.php`
4) You're good to go!
