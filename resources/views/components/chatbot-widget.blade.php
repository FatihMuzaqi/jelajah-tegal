@if (\App\Models\ChatbotSetting::isEnabled())
    {{-- Floating Chatbot Widget --}}
    <div id="jelajah-chatbot-root">
        {{-- Trigger Floating Button --}}
        <button id="chatbot-toggle-btn" class="chatbot-trigger" type="button" aria-label="Buka Chatbot Asisten Wisata">
            <div class="chatbot-trigger-icon">
                <i class="fa-solid fa-robot"></i>
                <span class="chatbot-pulse-ring"></span>
            </div>
            <span class="chatbot-trigger-text d-none d-sm-inline">Tanya Asisten AI</span>
        </button>

        {{-- Chat Window Card --}}
        <div id="chatbot-window" class="chatbot-card hidden" role="dialog" aria-modal="true"
            aria-labelledby="chatbot-title">
            {{-- Header --}}
            <div class="chatbot-header">
                <div class="chatbot-header-info">
                    <div class="chatbot-avatar">
                        <i class="fa-solid fa-wand-magic-sparkles"></i>
                    </div>
                    <div>
                        <h3 id="chatbot-title" class="chatbot-title">Asisten Wisata Tegal</h3>
                        <div class="chatbot-status">
                            <span class="status-dot-pulse"></span>
                            <span>Didukung Gemini AI &middot; Online</span>
                        </div>
                    </div>
                </div>
                <button id="chatbot-close-btn" class="chatbot-close-btn" type="button" aria-label="Tutup Chatbot">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            {{-- Messages Container --}}
            <div id="chatbot-messages-container" class="chatbot-messages">
                {{-- Welcome Message from Bot --}}
                <div class="chat-msg bot">
                    <div class="chat-bubble">
                        <p class="mb-1">Halo! 👋 Saya <strong>Asisten Cerdas Jelajah Tegal</strong> siap membantu
                            liburan Anda!</p>
                        <p class="mb-0 text-muted small">Anda bisa menanyakan rekomendasi wisata di Guci, kuliner sate
                            kambing khas Tegal, hotel nyaman, tiket event, atau rental kendaraan.</p>
                        <span class="chat-time">{{ date('H:i') }}</span>
                    </div>
                </div>
            </div>

            {{-- Quick Suggestion Chips --}}
            <div class="chatbot-chips">
                <button type="button" class="chat-chip"
                    data-question="Apa rekomendasi wisata hits di Tegal & Guci?">🏖️ Wisata Hits Guci</button>
                <button type="button" class="chat-chip"
                    data-question="Rekomendasi kuliner sate kambing & makanan khas Tegal yang wajib dicoba?">🍲 Kuliner
                    Khas Tegal</button>
                <button type="button" class="chat-chip"
                    data-question="Cari penginapan atau hotel bagus dekat wisata Tegal?">🏨 Penginapan & Hotel</button>
                <button type="button" class="chat-chip"
                    data-question="Bagaimana cara sewa mobil atau motor di Tegal?">🚗 Rental Kendaraan</button>
                <button type="button" class="chat-chip"
                    data-question="Ada event festival atau acara seru apa di Tegal?">🎪 Event & Festival</button>
            </div>

            {{-- Input Bar --}}
            <form id="chatbot-form" class="chatbot-input-bar">
                <input type="text" id="chatbot-input" class="chatbot-input"
                    placeholder="Ketik pertanyaan seputar Tegal..." autocomplete="off" maxlength="500" required>
                <button type="submit" id="chatbot-send-btn" class="chatbot-send-btn" aria-label="Kirim Pesan">
                    <i class="fa-solid fa-paper-plane"></i>
                </button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const root = document.getElementById('jelajah-chatbot-root');
            if (!root) return;

            const toggleBtn = document.getElementById('chatbot-toggle-btn');
            const closeBtn = document.getElementById('chatbot-close-btn');
            const chatWindow = document.getElementById('chatbot-window');
            const messagesContainer = document.getElementById('chatbot-messages-container');
            const form = document.getElementById('chatbot-form');
            const input = document.getElementById('chatbot-input');
            const sendBtn = document.getElementById('chatbot-send-btn');
            const chips = root.querySelectorAll('.chat-chip');

            let chatHistory = [];
            let isProcessing = false;

            // Toggle Chat Window
            function toggleChat() {
                const isHidden = chatWindow.classList.contains('hidden');
                if (isHidden) {
                    chatWindow.classList.remove('hidden');
                    input.focus();
                    scrollToBottom();
                } else {
                    chatWindow.classList.add('hidden');
                }
            }

            toggleBtn.addEventListener('click', toggleChat);
            closeBtn.addEventListener('click', toggleChat);

            // Click on Quick Suggestion Chips
            chips.forEach(chip => {
                chip.addEventListener('click', function() {
                    const question = this.getAttribute('data-question');
                    if (question && !isProcessing) {
                        input.value = question;
                        sendMessage(question);
                    }
                });
            });

            // Form Submit
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const text = input.value.trim();
                if (text && !isProcessing) {
                    sendMessage(text);
                }
            });

            function scrollToBottom() {
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }

            function getTimeString() {
                const now = new Date();
                return String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0');
            }

            // Format simple Markdown to HTML (Bold, Links, Bullet Points)
            function formatMarkdown(text) {
                let escaped = text
                    .replace(/&/g, "&amp;")
                    .replace(/</g, "&lt;")
                    .replace(/>/g, "&gt;");

                // Bold: **text**
                escaped = escaped.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');

                // Links: [text](url)
                escaped = escaped.replace(/\[(.*?)\]\((.*?)\)/g,
                    '<a href="$2" target="_blank" rel="noopener noreferrer">$1</a>');

                // Line breaks & bullet lists
                const lines = escaped.split('\n');
                let html = '';
                let inList = false;

                lines.forEach(line => {
                    const trimmed = line.trim();
                    if (trimmed.startsWith('• ') || trimmed.startsWith('- ') || trimmed.startsWith('* ')) {
                        if (!inList) {
                            html += '<ul class="ps-3 mb-2 mt-1">';
                            inList = true;
                        }
                        html += '<li>' + trimmed.substring(2) + '</li>';
                    } else if (/^\d+\.\s/.test(trimmed)) {
                        if (!inList) {
                            html += '<ol class="ps-3 mb-2 mt-1">';
                            inList = true;
                        }
                        html += '<li>' + trimmed.replace(/^\d+\.\s/, '') + '</li>';
                    } else {
                        if (inList) {
                            html += '</ul>';
                            inList = false;
                        }
                        if (trimmed.length > 0) {
                            html += '<p class="mb-2">' + trimmed + '</p>';
                        }
                    }
                });

                if (inList) html += '</ul>';
                return html;
            }

            // Append Message to UI
            function appendMessage(sender, content) {
                const msgDiv = document.createElement('div');
                msgDiv.className = `chat-msg ${sender}`;

                const bubbleDiv = document.createElement('div');
                bubbleDiv.className = 'chat-bubble';

                if (sender === 'user') {
                    bubbleDiv.textContent = content;
                } else {
                    bubbleDiv.innerHTML = formatMarkdown(content);
                }

                const timeSpan = document.createElement('span');
                timeSpan.className = 'chat-time';
                timeSpan.textContent = getTimeString();
                bubbleDiv.appendChild(timeSpan);

                msgDiv.appendChild(bubbleDiv);
                messagesContainer.appendChild(msgDiv);
                scrollToBottom();
            }

            // Show Typing Indicator
            function showTypingIndicator() {
                const typingDiv = document.createElement('div');
                typingDiv.className = 'chat-msg bot typing-indicator-wrapper';
                typingDiv.innerHTML = `
            <div class="chat-typing">
                <span></span>
                <span></span>
                <span></span>
            </div>
        `;
                messagesContainer.appendChild(typingDiv);
                scrollToBottom();
                return typingDiv;
            }

            // Send Message AJAX to Backend
            async function sendMessage(message) {
                isProcessing = true;
                input.value = '';
                input.disabled = true;
                sendBtn.disabled = true;

                appendMessage('user', message);
                const typingIndicator = showTypingIndicator();

                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                        'content') || '';

                    const response = await fetch('/chatbot/message', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify({
                            message: message,
                            history: chatHistory
                        })
                    });

                    const data = await response.json();
                    typingIndicator.remove();

                    if (data.success && data.reply) {
                        appendMessage('bot', data.reply);
                        chatHistory.push({
                            user: message,
                            bot: data.reply
                        });
                    } else {
                        appendMessage('bot', data.reply ||
                            'Maaf, terjadi kendala saat memproses jawaban. Silakan coba kembali.');
                    }
                } catch (error) {
                    console.error('Chatbot error:', error);
                    typingIndicator.remove();
                    appendMessage('bot',
                        'Mohon maaf, koneksi ke asisten AI terputus. Silakan coba beberapa saat lagi.');
                } finally {
                    isProcessing = false;
                    input.disabled = false;
                    sendBtn.disabled = false;
                    input.focus();
                    scrollToBottom();
                }
            }
        });
    </script>
@endif
