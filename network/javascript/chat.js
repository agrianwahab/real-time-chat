const form = document.querySelector(".typing-area"),
incoming_id = form.querySelector(".incoming_id").value,
fileInput = form.querySelector("#file"),
inputField = form.querySelector(".input-field"),
sendBtn = form.querySelector("button"),
chatBox = document.querySelector(".chat-box");

const modal = document.getElementById("imageModal");
const modalImg = document.getElementById("modalImage");
const filePreview = document.getElementById("filePreview");
const previewContent = filePreview.querySelector(".preview-content");

form.onsubmit = (e) => {
    e.preventDefault();
}

inputField.focus();

inputField.onkeyup = () => {
    if(inputField.value != "" || fileInput.files.length > 0){
        sendBtn.classList.add("active");
    }else{
        sendBtn.classList.remove("active");
    }
}

fileInput.onchange = () => {
    const files = fileInput.files;
    previewContent.innerHTML = '';
    
    if(files.length > 0) {
        filePreview.style.display = 'block';
        
        Array.from(files).forEach(file => {
            const reader = new FileReader();
            const previewItem = document.createElement('div');
            previewItem.className = 'preview-item';
            
            if(file.type.startsWith('image/')) {
                reader.onload = function(e) {
                    previewItem.innerHTML = `
                        <img src="${e.target.result}">
                        <span class="remove-preview">&times;</span>
                    `;
                }
                reader.readAsDataURL(file);
            } else {
                previewItem.innerHTML = `
                    <div class="file-icon">
                        <i class="fas fa-file"></i>
                    </div>
                    <div class="file-name">${file.name}</div>
                    <span class="remove-preview">&times;</span>
                `;
            }
            
            previewContent.appendChild(previewItem);
        });
        
        sendBtn.classList.add("active");
    } else {
        filePreview.style.display = 'none';
        if(inputField.value == '') {
            sendBtn.classList.remove("active");
        }
    }
}

sendBtn.onclick = () => {
    if (inputField.value.trim() === "" && fileInput.files.length === 0) {
        alert("Please enter a message or select a file.");
        return;
    }

    let formData = new FormData(form);

    // Hanya tambahkan file jika ada yang dipilih
    if (fileInput.files.length > 0) {
        formData.append("file", fileInput.files[0]);
    }

    const fileSize = fileInput.files[0]?.size || 0;

    if(fileSize > 5242880) { // 5MB limit
        alert("File size should not exceed 5MB");
        return;
    }

    sendBtn.disabled = true;

    fetch("php/insert-chat.php", {
        method: "POST",
        body: formData
    })
    .then(response => {
        inputField.value = "";
        fileInput.value = ""; // Reset file input
        filePreview.style.display = 'none';
        sendBtn.classList.remove("active");
        sendBtn.disabled = false;
        scrollToBottom();
    })
    .catch(error => {
        sendBtn.disabled = false;
        alert("Error sending message");
    });
}

chatBox.onmouseenter = () => {
    chatBox.classList.add("active");
}

chatBox.onmouseleave = () => {
    chatBox.classList.remove("active");
}

// Event listener untuk menghapus preview
previewContent.addEventListener('click', (e) => {
    if(e.target.classList.contains('remove-preview')) {
        const previewItem = e.target.closest('.preview-item');
        previewItem.remove();
        
        if(previewContent.children.length === 0) {
            fileInput.value = '';
            filePreview.style.display = 'none';
            if(inputField.value == '') {
                sendBtn.classList.remove("active");
            }
        }
    }
});

// Event listener untuk preview gambar full
document.addEventListener('click', (e) => {
    if(e.target.tagName === 'IMG' && e.target.closest('.chat')) {
        modal.style.display = "block";
        modalImg.src = e.target.src;
    }
});

// Close modal
document.querySelector('.close').onclick = () => {
    modal.style.display = "none";
}

// Close modal when clicking outside
window.onclick = (e) => {
    if(e.target == modal) {
        modal.style.display = "none";
    }
}

setInterval(() => {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "php/get-chat.php", true);
    xhr.onload = () => {
        if(xhr.readyState === XMLHttpRequest.DONE){
            if(xhr.status === 200){
                let data = xhr.response;
                chatBox.innerHTML = data;
                if(!chatBox.classList.contains("active")){
                    scrollToBottom();
                }
            }
        }
    }
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.send("incoming_id="+incoming_id);
}, 500);

function scrollToBottom(){
    chatBox.scrollTop = chatBox.scrollHeight;
}

function deleteMessage(msgId) {
    if(confirm("Are you sure you want to delete this message?")) {
        let formData = new FormData();
        formData.append("msg_id", msgId);
        
        fetch("php/delete-chat.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            if(data === "success") {
                document.querySelector(`.chat[data-msg-id="${msgId}"]`).remove();
            } else {
                alert("Error deleting message");
            }
        })
        .catch(error => {
            alert("Error deleting message");
        });
    }
}