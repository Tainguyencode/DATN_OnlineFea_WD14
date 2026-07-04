import './bootstrap';
import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';

window.Alpine = Alpine;
window.Chart = Chart;

Alpine.start();

// Initialize Theme on Load
(function () {
    const savedTheme = localStorage.getItem('theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
})();

// Bind functions to window so they are globally accessible from inline HTML event handlers
window.toggleTheme = function () {
    const isDark = document.documentElement.classList.contains('dark');
    if (isDark) {
        document.documentElement.classList.remove('dark');
        localStorage.setItem('theme', 'light');
    } else {
        document.documentElement.classList.add('dark');
        localStorage.setItem('theme', 'dark');
    }
};

window.toggleChat = function () {
    const drawer = document.getElementById('ai-chat-drawer');
    if (drawer) {
        drawer.classList.toggle('translate-x-full');
    }
};

window.sendChatMessage = function () {
    const input = document.getElementById('chat-input');
    const messagesContainer = document.getElementById('chat-messages');
    if (!input || !messagesContainer || !input.value.trim()) return;

    const messageText = input.value.trim();
    input.value = '';

    // Append User Message
    const userBubble = document.createElement('div');
    userBubble.className = 'flex justify-end mb-4';
    userBubble.innerHTML = `
        <div class="bg-indigo-600 text-white rounded-2xl rounded-tr-none px-4 py-2.5 max-w-[80%] text-sm shadow-sm">
            ${escapeHtml(messageText)}
        </div>
    `;
    messagesContainer.appendChild(userBubble);
    scrollChatToBottom();

    // Show Typing Indicator
    const typingBubble = document.createElement('div');
    typingBubble.className = 'flex justify-start mb-4 opacity-75';
    typingBubble.id = 'chat-typing-indicator';
    typingBubble.innerHTML = `
        <div class="bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 rounded-2xl rounded-tl-none px-4 py-3 text-sm">
            <span class="inline-flex gap-1 items-center">
                <span class="w-1.5 h-1.5 bg-slate-500 rounded-full animate-bounce"></span>
                <span class="w-1.5 h-1.5 bg-slate-500 rounded-full animate-bounce [animation-delay:0.2s]"></span>
                <span class="w-1.5 h-1.5 bg-slate-500 rounded-full animate-bounce [animation-delay:0.4s]"></span>
            </span>
        </div>
    `;
    messagesContainer.appendChild(typingBubble);
    scrollChatToBottom();

    // Simulate AI Reply
    setTimeout(() => {
        const indicator = document.getElementById('chat-typing-indicator');
        if (indicator) indicator.remove();

        const replyText = getAiResponse(messageText);
        const aiBubble = document.createElement('div');
        aiBubble.className = 'flex justify-start mb-4';
        aiBubble.innerHTML = `
            <div class="bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 rounded-2xl rounded-tl-none px-4 py-2.5 max-w-[85%] text-sm shadow-sm leading-relaxed">
                ${replyText}
            </div>
        `;
        messagesContainer.appendChild(aiBubble);
        scrollChatToBottom();
    }, 1000);
};

window.sendPresetMessage = function (text) {
    const input = document.getElementById('chat-input');
    if (input) {
        input.value = text;
        window.sendChatMessage();
    }
};

function scrollChatToBottom() {
    const container = document.getElementById('chat-messages');
    if (container) {
        container.scrollTop = container.scrollHeight;
    }
}

function escapeHtml(unsafe) {
    return unsafe
         .replace(/&/g, "&amp;")
         .replace(/</g, "&lt;")
         .replace(/>/g, "&gt;")
         .replace(/"/g, "&quot;")
         .replace(/'/g, "&#039;");
}

function getAiResponse(msg) {
    const text = msg.toLowerCase();
    if (text.includes('lộ trình') || text.includes('roadmap') || text.includes('học')) {
        return 'Để học tập hiệu quả trên **Website học online FEA**, bạn nên bắt đầu từ các danh mục cơ bản như **Lập trình Web** với khóa học *Laravel từ Zero đến Hero*. Sau đó nâng cao lên *React.js Masterclass* và *Data Science*.';
    }
    if (text.includes('đồ án') || text.includes('datn') || text.includes('tốt nghiệp')) {
        return 'Chào bạn! Website học online FEA hỗ trợ đầy đủ quản lý học trực tuyến, tương tác giữa giảng viên và học viên, báo cáo tiến độ trực tuyến, và tích hợp AI hỗ trợ học tập.';
    }
    if (text.includes('chào') || text.includes('hello') || text.includes('hi')) {
        return 'Xin chào! Mình là **FEA AI Assistant**. Mình có thể giúp gì cho quá trình học tập online của bạn hôm nay?';
    }
    if (text.includes('giảng viên') || text.includes('admin') || text.includes('giáo viên')) {
        return 'Giảng viên trên Website học online FEA có quyền tạo chương mục, tải tài liệu học tập, soạn bài tập, chấm điểm và trao đổi trực tiếp với học viên. Quản trị viên sẽ phê duyệt các khóa học chất lượng trước khi phát hành.';
    }
    return 'Cảm ơn bạn đã trò chuyện! Mình ghi nhận câu hỏi. Bạn có thể tham khảo lộ trình học lập trình Web, quản lý đồ án tốt nghiệp, hoặc liên hệ giảng viên hướng dẫn để được giải đáp chuyên sâu.';
}
