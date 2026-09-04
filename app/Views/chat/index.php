<?= $this->extend('layouts/user_layout') ?>

<?= $this->section('content') ?>
<style>
    .chat-wrapper { height: calc(100vh - 110px); min-height: 500px; display: flex; background: #fff; border-radius: 16px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); overflow: hidden; margin-top: -5px; }
    
    /* Sidebar */
    .chat-sidebar { width: 350px; border-right: 1px solid #edf2f7; display: flex; flex-direction: column; background: #fff; }
    .sidebar-header { padding: 18px 20px; border-bottom: 1px solid #edf2f7; display: flex; align-items: center; justify-content: space-between; }
    .sidebar-title { font-weight: 700; font-size: 1.25rem; color: #2d3748; margin: 0; }
    
    .search-box { padding: 12px 15px; background: #f7fafc; border-bottom: 1px solid #edf2f7; }
    .search-input-wrapper { position: relative; }
    .search-input-wrapper i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #a0aec0; }
    .search-input { width: 100%; padding: 8px 15px 8px 38px; border-radius: 20px; border: 1px solid #e2e8f0; background: #fff; outline: none; transition: all 0.3s; font-size: 0.9rem; }
    .search-input:focus { border-color: #4299e1; box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.15); }
    
    .contact-list { flex: 1; overflow-y: auto; }
    .contact-item { padding: 12px 18px; display: flex; align-items: center; cursor: pointer; transition: all 0.2s; border-bottom: 1px solid #f7fafc; }
    .contact-item:hover { background: #ebf8ff; }
    .contact-item.active { background: #ebf8ff; border-left: 4px solid #4299e1; }
    
    .avatar-wrapper { position: relative; margin-right: 12px; }
    .contact-avatar { width: 44px; height: 44px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 1.1rem; color: white; }
    .status-indicator { position: absolute; bottom: 2px; right: 2px; width: 12px; height: 12px; border-radius: 50%; border: 2px solid #fff; }
    .status-online { background: #48bb78; }
    .status-offline { background: #a0aec0; }
    
    .contact-info { flex: 1; min-width: 0; }
    .contact-top { display: flex; justify-content: space-between; margin-bottom: 3px; }
    .contact-name { font-weight: 600; color: #2d3748; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-size: 0.95rem; }
    .contact-time { font-size: 0.75rem; color: #a0aec0; }
    .contact-bottom { display: flex; justify-content: space-between; align-items: center; }
    .contact-msg { font-size: 0.82rem; color: #718096; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; flex: 1; margin-right: 8px; }
    .badge-unread { background: #e53e3e; color: white; border-radius: 12px; padding: 2px 8px; font-size: 0.7rem; font-weight: 700; }
    
    /* Main Chat Area */
    .chat-main { flex: 1; display: flex; flex-direction: column; background: #f8fafc; }
    .chat-header { padding: 12px 20px; background: #fff; border-bottom: 1px solid #edf2f7; display: flex; align-items: center; }
    .chat-header .contact-avatar { width: 40px; height: 40px; margin-right: 12px; }
    .header-info h5 { margin: 0 0 2px 0; font-weight: 600; color: #2d3748; font-size: 1rem; }
    .header-status { font-size: 0.78rem; color: #718096; }
    .header-status.online { color: #48bb78; }
    
    .chat-messages { flex: 1; padding: 20px; overflow-y: auto; display: flex; flex-direction: column; gap: 10px; background-image: url('data:image/svg+xml,%3Csvg width=\"20\" height=\"20\" viewBox=\"0 0 20 20\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"%23e2e8f0\" fill-opacity=\"0.4\" fill-rule=\"evenodd\"%3E%3Ccircle cx=\"3\" cy=\"3\" r=\"3\"/%3E%3Ccircle cx=\"13\" cy=\"13\" r=\"3\"/%3E%3C/g%3E%3C/svg%3E'); }
    
    .message-wrapper { display: flex; max-width: 80%; }
    .message-wrapper.outgoing { align-self: flex-end; flex-direction: row-reverse; }
    .message-wrapper.incoming { align-self: flex-start; }
    
    .message-bubble { padding: 10px 14px; border-radius: 16px; position: relative; box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
    .incoming .message-bubble { background: #fff; border-bottom-left-radius: 4px; color: #2d3748; }
    .outgoing .message-bubble { background: #4299e1; border-bottom-right-radius: 4px; color: #fff; }
    
    .sender-name { font-size: 0.75rem; font-weight: 600; margin-bottom: 4px; color: #ed8936; }
    .message-text { font-size: 0.9rem; line-height: 1.4; word-wrap: break-word; }
    .message-time { font-size: 0.68rem; margin-top: 4px; text-align: right; opacity: 0.7; }
    
    .chat-input-area { padding: 15px 20px; background: #fff; border-top: 1px solid #edf2f7; display: flex; gap: 10px; align-items: center; }
    .input-wrapper { flex: 1; position: relative; }
    .chat-input { width: 100%; border: 1px solid #e2e8f0; border-radius: 25px; padding: 10px 18px; outline: none; transition: all 0.3s; background: #f7fafc; font-size: 0.9rem; }
    .chat-input:focus { border-color: #4299e1; background: #fff; box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.15); }
    .btn-send { background: #4299e1; color: white; border: none; border-radius: 50%; width: 42px; height: 42px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 6px rgba(66, 153, 225, 0.25); flex-shrink: 0; }
    .btn-send:hover { background: #3182ce; transform: translateY(-2px); box-shadow: 0 6px 8px rgba(66, 153, 225, 0.3); }
    .btn-send:active { transform: translateY(0); }
    
    .no-chat-selected { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #a0aec0; background: #f8fafc; padding: 20px; text-align: center; }
    .no-chat-icon { width: 100px; height: 100px; border-radius: 50%; background: #edf2f7; display: flex; align-items: center; justify-content: center; margin-bottom: 16px; color: #cbd5e1; }
    
    /* Colors for avatars */
    .bg-color-0 { background: #4299e1; } .bg-color-1 { background: #48bb78; }
    .bg-color-2 { background: #ed8936; } .bg-color-3 { background: #9f7aea; }
    .bg-color-4 { background: #f56565; } .bg-color-group { background: #4a5568; }
    
    @media (max-width: 768px) {
        .chat-wrapper { height: calc(100vh - 90px); min-height: 450px; border-radius: 0; margin-top: 0; }
        .chat-sidebar { width: 100%; }
        .chat-main { display: none; position: absolute; top: 0; left: 0; right: 0; bottom: 0; z-index: 10; }
        .chat-main.active-mobile { display: flex; }
        .back-btn { display: block !important; margin-right: 12px; cursor: pointer; color: #4a5568; }
        .message-wrapper { max-width: 90%; }
    }
    .back-btn { display: none; }
</style>

<div class="container-fluid px-0 px-md-3 py-0 py-md-2">
    <div class="chat-wrapper position-relative">
        
        <!-- Sidebar -->
        <div class="chat-sidebar" id="chatSidebar">
            <div class="sidebar-header">
                <h2 class="sidebar-title">Obrolan Warga</h2>
                <div class="dropdown">
                    <button class="btn btn-sm btn-light rounded-circle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#" onclick="fetchContacts()"><i class="fas fa-sync me-2"></i> Refresh</a></li>
                        <?php if (isset($user['role']) && $user['role'] === 'admin'): ?>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="<?= base_url('chat/create-default-group') ?>"><i class="fas fa-users-cog me-2"></i> Buat Grup Diskusi Warga</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
            <div class="search-box">
                <div class="search-input-wrapper">
                    <i class="fas fa-search"></i>
                    <input type="text" class="search-input" id="searchInput" placeholder="Cari pesan atau kontak...">
                </div>
            </div>
            <div class="contact-list" id="contactList">
                <div class="text-center p-4 text-muted">
                    <div class="spinner-border spinner-border-sm me-2" role="status"></div> Memuat...
                </div>
            </div>
        </div>
        
        <!-- Main Area -->
        <div class="chat-main" id="chatMain" style="display: none;">
            <div class="chat-header">
                <i class="fas fa-arrow-left back-btn fs-4" onclick="closeMobileChat()"></i>
                <div class="contact-avatar" id="activeAvatar"></div>
                <div class="header-info">
                    <h5 id="activeName">Memuat...</h5>
                    <div class="header-status" id="activeStatus"></div>
                </div>
            </div>
            
            <div class="chat-messages" id="chatMessages">
                <!-- Messages load here -->
            </div>
            
            <div class="chat-input-area">
                <input type="hidden" id="activeContactId" value="">
                <input type="hidden" id="activeContactType" value="">
                <div class="input-wrapper">
                    <input type="text" class="chat-input" id="messageInput" placeholder="Ketik pesan..." onkeypress="handleKeyPress(event)" autocomplete="off">
                </div>
                <button class="btn-send" onclick="sendMessage()">
                    <i class="fas fa-paper-plane fs-6"></i>
                </button>
            </div>
        </div>
        
        <!-- Placeholder when no contact is selected -->
        <div class="no-chat-selected d-none d-md-flex" id="noChatSelected">
            <div class="no-chat-icon">
                <i class="fas fa-comments fa-3x"></i>
            </div>
            <h4 class="fw-bold text-dark">Obrolan Komunitas Warga</h4>
            <p class="text-muted small">Pilih kontak atau grup untuk memulai percakapan</p>
        </div>
    </div>
</div>

<script>
    const currentUserId = <?= $user['id'] ?>;
    let pollInterval = null;
    let pingInterval = null;
    let allContacts = [];
    let currentContactId = null;

    document.addEventListener("DOMContentLoaded", () => {
        fetchContacts();
        startPing();
        
        document.getElementById('searchInput').addEventListener('input', function(e) {
            renderContacts(e.target.value.toLowerCase());
        });
    });

    function startPing() {
        sendPing();
        pingInterval = setInterval(sendPing, 60000);
    }
    
    function sendPing() {
        fetch('<?= base_url('api/chat/ping') ?>', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        }).catch(err => console.log('Ping failed'));
    }

    function fetchContacts() {
        fetch('<?= base_url('api/chat/contacts') ?>', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(res => {
            if (res.status === 'success') {
                allContacts = res.data;
                renderContacts(document.getElementById('searchInput').value.toLowerCase());
                
                if(currentContactId) {
                    const activeContact = allContacts.find(c => c.id == currentContactId);
                    if(activeContact) updateHeaderStatus(activeContact);
                }
            }
        })
        .catch(err => console.error("Gagal memuat kontak:", err));
    }

    function renderContacts(searchQuery = '') {
        const container = document.getElementById('contactList');
        container.innerHTML = '';
        
        const filtered = allContacts.filter(c => c.name.toLowerCase().includes(searchQuery));
        
        if (filtered.length === 0) {
            container.innerHTML = `<div class="text-center p-4 text-muted">Kontak tidak ditemukan.</div>`;
            return;
        }
        
        filtered.forEach(c => {
            const isActive = currentContactId == c.id ? 'active' : '';
            const colorClass = c.type === 'group' ? 'bg-color-group' : `bg-color-${c.id % 5}`;
            const statusDot = c.type === 'user' ? `<div class="status-indicator ${c.is_online ? 'status-online' : 'status-offline'}"></div>` : '';
            
            const badge = c.unread_count > 0 ? `<div class="badge-unread">${c.unread_count}</div>` : '';
            const msgPreview = c.last_message || 'Belum ada pesan';
            const iconType = c.type === 'group' ? '<i class="fas fa-users me-1"></i>' : '';
            
            const html = `
                <div class="contact-item ${isActive}" id="contact-${c.id}" onclick="selectContact(${c.id}, '${c.type}')">
                    <div class="avatar-wrapper">
                        <div class="contact-avatar ${colorClass}">${c.avatar}</div>
                        ${statusDot}
                    </div>
                    <div class="contact-info">
                        <div class="contact-top">
                            <div class="contact-name">${c.name}</div>
                            <div class="contact-time">${c.last_time}</div>
                        </div>
                        <div class="contact-bottom">
                            <div class="contact-msg">${iconType}${escapeHtml(msgPreview)}</div>
                            ${badge}
                        </div>
                    </div>
                </div>
            `;
            container.innerHTML += html;
        });
    }

    function selectContact(id, type) {
        currentContactId = id;
        const contact = allContacts.find(c => c.id == id && c.type == type);
        if(!contact) return;
        
        document.querySelectorAll('.contact-item').forEach(el => el.classList.remove('active'));
        const el = document.getElementById(`contact-${id}`);
        if(el) el.classList.add('active');
        
        const noChat = document.getElementById('noChatSelected');
        if (noChat) noChat.style.display = 'none';

        const chatMain = document.getElementById('chatMain');
        chatMain.style.display = 'flex';
        chatMain.classList.add('active-mobile');
        
        document.getElementById('activeContactId').value = id;
        document.getElementById('activeContactType').value = type;
        document.getElementById('activeName').innerText = contact.name;
        
        const avatarEl = document.getElementById('activeAvatar');
        avatarEl.innerText = contact.avatar;
        avatarEl.className = `contact-avatar ${type === 'group' ? 'bg-color-group' : `bg-color-${id % 5}`}`;
        
        updateHeaderStatus(contact);
        
        contact.unread_count = 0;
        renderContacts(document.getElementById('searchInput').value.toLowerCase());
        
        loadMessages(id, type);
        
        if(pollInterval) clearInterval(pollInterval);
        pollInterval = setInterval(() => {
            loadMessages(id, type, false);
            fetchContacts();
        }, 3000);
    }
    
    function updateHeaderStatus(contact) {
        const statusEl = document.getElementById('activeStatus');
        if (contact.type === 'group') {
            statusEl.innerText = 'Grup Diskusi Warga';
            statusEl.className = 'header-status';
        } else {
            if (contact.is_online) {
                statusEl.innerText = 'Online';
                statusEl.className = 'header-status online';
            } else {
                let text = 'Offline';
                if(contact.last_seen) {
                    const date = new Date(contact.last_seen);
                    const isToday = (new Date().toDateString() === date.toDateString());
                    const time = date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                    text = `Terakhir dilihat: ${isToday ? 'Hari ini' : date.toLocaleDateString()} ${time}`;
                }
                statusEl.innerText = text;
                statusEl.className = 'header-status';
            }
        }
    }
    
    function closeMobileChat() {
        document.getElementById('chatMain').classList.remove('active-mobile');
        document.getElementById('chatMain').style.display = 'none';
    }

    function loadMessages(id, type, scroll = true) {
        fetch(`<?= base_url('api/chat/messages') ?>/${id}/${type}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(res => {
            if (res.status === 'success' && res.data) {
                renderMessages(res.data, scroll);
            }
        });
    }

    function renderMessages(messages, scroll) {
        const container = document.getElementById('chatMessages');
        container.innerHTML = '';
        
        if(messages.length === 0) {
            container.innerHTML = `<div class="text-center p-4 text-muted">Belum ada percakapan. Mulai kirim pesan!</div>`;
            return;
        }
        
        const type = document.getElementById('activeContactType').value;
        
        messages.forEach(msg => {
            const isOutgoing = msg.sender_id == currentUserId;
            const time = new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
            
            let senderNameHtml = '';
            if (!isOutgoing && type === 'group' && msg.sender_name) {
                let hash = 0;
                for (let i = 0; i < msg.sender_name.length; i++) {
                    hash = msg.sender_name.charCodeAt(i) + ((hash << 5) - hash);
                }
                const colorCode = `hsl(${Math.abs(hash) % 360}, 75%, 45%)`;
                senderNameHtml = `<div class="sender-name" style="color: ${colorCode}; padding-bottom: 2px;">${escapeHtml(msg.sender_name)}</div>`;
            }
            
            const div = document.createElement('div');
            div.className = `message-wrapper ${isOutgoing ? 'outgoing' : 'incoming'}`;
            div.innerHTML = `
                <div class="message-bubble">
                    ${senderNameHtml}
                    <div class="message-text">${escapeHtml(msg.pesan).replace(/\\n/g, '<br>')}</div>
                    <div class="message-time">${time}</div>
                </div>
            `;
            container.appendChild(div);
        });
        
        if(scroll) {
            container.scrollTop = container.scrollHeight;
        }
    }

    function sendMessage() {
        const input = document.getElementById('messageInput');
        const contactId = document.getElementById('activeContactId').value;
        const type = document.getElementById('activeContactType').value;
        const pesan = input.value.trim();
        
        if(!pesan || !contactId) return;
        
        input.value = '';
        input.focus();
        
        const formData = new FormData();
        formData.append('receiver_id', contactId);
        formData.append('type', type);
        formData.append('pesan', pesan);
        
        fetch('<?= base_url('api/chat/send') ?>', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(async response => {
            if(!response.ok) throw new Error(await response.text());
            return response.json();
        })
        .then(data => {
            if(data.status === 'success') {
                loadMessages(contactId, type, true);
                fetchContacts();
            }
        })
        .catch(err => console.error(err));
    }

    function handleKeyPress(e) {
        if(e.key === 'Enter') {
            sendMessage();
        }
    }

    function escapeHtml(unsafe) {
        if (!unsafe) return '';
        return unsafe
             .replace(/&/g, "&amp;")
             .replace(/</g, "&lt;")
             .replace(/>/g, "&gt;")
             .replace(/"/g, "&quot;")
             .replace(/'/g, "&#039;");
    }
</script>
<?= $this->endSection() ?>
