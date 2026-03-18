function scrollToBottom() {
    const chatBox = document.querySelector(".chat-box");
    if (!chatBox) return;
    chatBox.scrollTop = chatBox.scrollHeight;
}

function isNearBottom(chatBox, threshold = 80) {
    if (!chatBox) return true;
    const distanceFromBottom = chatBox.scrollHeight - (chatBox.scrollTop + chatBox.clientHeight);
    return distanceFromBottom <= threshold;
}

const form = document.querySelector(".typing-area"),
incoming_id = form.querySelector(".incoming_id").value,
inputField = form.querySelector(".input-field"),
sendBtn = form.querySelector("button"),
chatBox = document.querySelector(".chat-box");

let firstLoad = true;
let userScrolledUp = false;

form.onsubmit = (e)=>{
    e.preventDefault();
}

inputField.focus();
inputField.onkeyup = ()=>{
    if(inputField.value != ""){
        sendBtn.classList.add("active");
    }else{
        sendBtn.classList.remove("active");
    }
}

sendBtn.onclick = ()=>{
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "php/insert-chat.php", true);
    xhr.onload = ()=>{
      if(xhr.readyState === XMLHttpRequest.DONE){
          if(xhr.status === 200){
              inputField.value = "";
              scrollToBottom();
          }
      }
    }
    let formData = new FormData(form);
    xhr.send(formData);
}

chatBox.onscroll = ()=>{
    userScrolledUp = !isNearBottom(chatBox);
}

function fetchMessages() {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "php/get-chat.php", true);
    xhr.onload = ()=>{
      if(xhr.readyState === XMLHttpRequest.DONE){
          if(xhr.status === 200){
            const shouldStickToBottom = firstLoad || !userScrolledUp || isNearBottom(chatBox);
            let data = xhr.response;
            chatBox.innerHTML = data;
            if(shouldStickToBottom){
                scrollToBottom();
                // Ensure final position after layout settles.
                requestAnimationFrame(scrollToBottom);
                setTimeout(scrollToBottom, 40);
                userScrolledUp = false;
            }
            firstLoad = false;
          }
      }
    }
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.send("incoming_id="+incoming_id);
}

// Load immediately so opening chat lands on latest message.
fetchMessages();
setInterval(fetchMessages, 500);
