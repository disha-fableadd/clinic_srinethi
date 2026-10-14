<style>
    button:focus{
        outline: none !important;
    }
    .chatbot-button {
        position: fixed;
        bottom: 90px;
        right: 20px;
        background-color: #cfece0;
        color: #2e2e2e;
        padding: 10px;
        border-radius: 50px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 10px rgba(0,0,0,0.3);
        z-index: 1000;
        font-family: 'Montserrat', sans-serif;
        font-weight: 600;
        font-size: 13px;
        border: none;
        transition: transform 0.2s, width 0.2s, padding 0.2s;
        width: 58px;
        height: 58px;
        overflow: hidden;
    }
    .chatbot-button:hover {
        transform: scale(1.05);
        width: auto;
        padding: 10px 16px;
    }

    .chatbot-button .chatbot-text {
        display: none;
        white-space: nowrap;
        margin-right: 6px;
    }

    .chatbot-button:hover .chatbot-text {
        display: inline-block;
    }

    .chatbot-button i {
        display: inline-block;
        background: white;
        color: #f89884;
        padding: 10px;
        border-radius: 50%;
        margin-left: 0;
        font-size: 20px;
    }
    .chatbot-box {
        position: fixed;
        bottom: 80px;
        right: 20px;
        width: 350px;
        height: 500px;
        max-height: calc(100vh - 165px);
        max-height: calc(100dvh - 165px);
        background: white;
        border-radius: 15px;
        box-shadow: 0 5px 25px rgba(0,0,0,0.2);
        display: none;
        flex-direction: column;
        z-index: 1000;
        overflow: hidden;
        border: 1px solid #ddd;
    }
    .chatbot-header {
        background: #cfece0;
        color: #2e2e2e;
        padding: 10px 15px;
        font-weight: 700;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-shrink: 0;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .chatbot-body {
        padding: 15px;
        height: 445px; /* Fixed height for scroll */
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 15px;
        background: #f8f9fa;
    }
    .chat-message {
        max-width: 85%;
        padding: 12px 16px;
        border-radius: 18px;
        font-size: 14px;
        line-height: 1.5;
        position: relative;
        word-wrap: break-word;
        animation: fadeIn 0.3s ease;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .user-message {
        align-self: flex-end;
        background: #cfece0;
        color: #2e2e2e;
        border-bottom-right-radius: 4px;
        font-weight: 500;
    }
    .bot-message {
        align-self: flex-start;
        background: white;
        color: #2e2e2e;
        border-bottom-left-radius: 4px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        width: 100%;
        box-sizing: border-box;
        border: 1px solid #eee;
    }
    .chatbot-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
        width: 100%;
    }
    .chatbot-menu-btn {
        background: white;
        border: 1.5px solid #cfece0;
        color: #2e2e2e;
        padding: 8px 5px;
        border-radius: 10px;
        cursor: pointer;
        text-align: center;
        font-weight: 600;
        font-size: 12px;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100%;
    }
    .chatbot-menu-btn:hover {
        background: #cfece0;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .chatbot-back-btn {
        background: #cfece0;
        color: #666;
        border: 1.5px solid #ddd;
        padding: 8px;
        border-radius: 10px;
        cursor: pointer;
        text-align: center;
        width: 100%;
        font-weight: 600;
        font-size: 12px;
        transition: all 0.2s;
    }
    .chatbot-back-btn:hover {
        background: #eee;
        color: #333;
    }
    .full-width {
        grid-column: span 2;
    }
    .chatbot-data-item {
        background: #ffffff;
        padding: 12px;
        border-radius: 10px;
        border-left: 4px solid #cfece0;
        margin-bottom: 10px;
        font-size: 13px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    .chatbot-button { animation: chatbot-dance 1.2s infinite; }

    @keyframes chatbot-dance {
        0%   { transform: translateY(0) rotate(0deg); }
        20%  { transform: translateY(-2px) rotate(-6deg); }
        40%  { transform: translateY(1px) rotate(6deg); }
        60%  { transform: translateY(-2px) rotate(-4deg); }
        80%  { transform: translateY(1px) rotate(4deg); }
        100% { transform: translateY(0) rotate(0deg); }
    }

    .chatbot-data-item strong {
        color: #555;
    }
    
    /* Select2 Chatbot Adjustments */
    .chatbot-box .select2-container {
        width: 100% !important;
        margin-bottom: 10px;
    }
    .chatbot-box .select2-container--default .select2-selection--single {
         box-shadow: none;
        font-size: 14px;
        min-height: 40px !important;
        border-radius: 50px !important;
        padding: 0.469rem 0.75rem;
        border-color: rgb(207, 236, 224) !important;
    }
    .chatbot-box .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 38px;
    }
    .select2-container--open {
        z-index: 9999999 !important;
    }
    
    /* Responsive Styles */
    @media (max-width: 1024px) {
        .chatbot-box {
            width: 400px;
            height: 60vh;
        }
    }

    @media (max-width: 768px) {
        .chatbot-box {
            width: 80%;
            height: 70vh;
            right: 10%;
        }
    }

    @media (max-width: 480px) {
        .chatbot-box {
            width: 94%;
            height: 80vh;
            right: 3%;
            bottom: 80px;
        }
        .chatbot-button {
            padding: 6px 12px;
            font-size: 12px;
            bottom: 35px;
            right: 15px;
        }
        .chatbot-header {
            padding: 12px 15px;
        }
        .chatbot-body {
            padding: 12px;
        }
        .chatbot-menu-btn {
            padding: 10px 5px;
            font-size: 12px;
        }
    }

    @media (max-width: 767px) {
        .chatbot-button {
            bottom: calc(76px + env(safe-area-inset-bottom));
            z-index: 1041;
        }
        .chatbot-box {
            bottom: calc(76px + env(safe-area-inset-bottom));
            max-height: calc(100vh - 88px - env(safe-area-inset-bottom));
            max-height: calc(100dvh - 88px - env(safe-area-inset-bottom));
            z-index: 1041;
        }
    }

    .typing-indicator {
        display: flex;
        padding: 10px 15px;
        background: white;
        border-radius: 15px;
        width: fit-content;
        gap: 5px;
        align-self: flex-start;
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        margin-bottom: 15px;
    }
    .typing-dot {
        width: 6px;
        height: 6px;
        background: #939598;
        border-radius: 50%;
        animation: typing 1.4s infinite;
    }
    .typing-dot:nth-child(2) { animation-delay: 0.2s; }
    .typing-dot:nth-child(3) { animation-delay: 0.4s; }
    @keyframes typing {
        0%, 60%, 100% { transform: translateY(0); }
        30% { transform: translateY(-4px); }
    }
</style>

<button class="chatbot-button" id="chatbotToggle">
    <span class="chatbot-text">How Can I Help?</span>
    <i class="fas fa-robot"></i>
</button>

<div class="chatbot-box" id="chatbotBox">
    <div class="chatbot-header">
        <span>Assistant</span>
        <button id="chatbotClose" style="background:none; border:none; color:#2e2e2e; cursor:pointer;"><i class="fas fa-times"></i></button>
    </div>
    <div class="chatbot-body" id="chatbotBody"></div>
</div>

<script>
$(document).ready(function() {
    let selectedPatientId = null;
    let selectedType = null;
    const chatbotBody = $('#chatbotBody');

    function scrollToBottom() {
        chatbotBody.scrollTop(chatbotBody[0].scrollHeight);
    }

    function appendUserMessage(text) {
        chatbotBody.append(`<div class="chat-message user-message">${text}</div>`);
        scrollToBottom();
    }

    function appendBotMessage(content) {
        const msgId = 'bot-' + Date.now();
        chatbotBody.append(`<div class="chat-message bot-message" id="${msgId}">${content}</div>`);
        scrollToBottom();
        return msgId;
    }

    function showTyping() {
        const id = 'typing-' + Date.now();
        chatbotBody.append(`
            <div class="typing-indicator" id="${id}">
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
            </div>
        `);
        scrollToBottom();
        return id;
    }

    function botReply(content, delay = 600) {
        const typingId = showTyping();
        setTimeout(() => {
            $(`#${typingId}`).remove();
            appendBotMessage(content);
        }, delay);
    }

    function renderMain() {
        selectedPatientId = null;
        botReply(`
            <div class="chatbot-grid">
                <button class="chatbot-menu-btn" onclick="handleMenu('appointment', 'Appointment')">Appointment</button>
                <button class="chatbot-menu-btn" onclick="handleMenu('followup', 'Followups')">Followups</button>
                <button class="chatbot-menu-btn" onclick="handleMenu('treatment', 'Treatment')">Treatment</button>
                <button class="chatbot-menu-btn" onclick="handleMenu('payment', 'Payment')">Payment</button>
                <button class="chatbot-menu-btn full-width" onclick="handleMenu('patient', 'Patient')">Patient</button>
            </div>
        `);
    }

    window.handleMenu = function(type, label) {
        appendUserMessage(label);
        if (type === 'patient') {
            renderPatientList();
        } else {
            selectedType = type;
            renderSubMenu(type);
        }
    };

    function renderSubMenu(type) {
        botReply(`
            <div class="chatbot-grid">
                <button class="chatbot-menu-btn" onclick="fetchData('${type}', 'today', 'Today')">Today</button>
                <button class="chatbot-menu-btn" onclick="fetchData('${type}', 'week', 'This Week')">This Week</button>
                <button class="chatbot-menu-btn" onclick="fetchData('${type}', 'month', 'This Month')">This Month</button>
                <button class="chatbot-back-btn full-width" onclick="goBack()">Back</button>
            </div>
        `);
    }

    window.renderPatientList = function() {
        const loadingId = appendBotMessage('<div class="chatbot-loading" style="text-align:center; padding:10px;"><i class="fas fa-spinner fa-spin"></i> Loading Patients...</div>');
        const branchId = localStorage.getItem("selectedBranchId");
        
        $.get('/chatbot/patients', { branch_id: branchId }, function(patients) {
            let options = patients.map(p => `<option value="${p.id}">${p.fullname}</option>`).join('');
            $(`#${loadingId}`).html(`
                <div style="margin-bottom:10px;">
                    <select class="patient-select" style="width: 100%;">
                        <option value="">Select Patient</option>
                        ${options}
                    </select>
                </div>
                <button class="chatbot-back-btn full-width" onclick="goBack()">Back</button>
            `);
            
            const currentSelect = $(`#${loadingId}`).find('.patient-select');
            if ($.fn.select2) {
                currentSelect.select2({
                    dropdownParent: $('#chatbotBox')
                }).on('change', function() {
                    selectedPatientId = $(this).val();
                    if (selectedPatientId) {
                        const name = currentSelect.find("option:selected").text();
                        appendUserMessage(name);
                        renderPatientActions();
                    }
                });
            } else {
                currentSelect.on('change', function() {
                    selectedPatientId = $(this).val();
                    if (selectedPatientId) {
                        const name = currentSelect.find("option:selected").text();
                        appendUserMessage(name);
                        renderPatientActions();
                    }
                });
            }
        });
    }

    window.renderPatientActions = function() {
        botReply(`
            <div class="chatbot-grid">
                <button class="chatbot-menu-btn" onclick="handlePatientAction('appointment', 'Appointment')">Appointment</button>
                <button class="chatbot-menu-btn" onclick="handlePatientAction('treatment', 'Treatment')">Treatment</button>
                <button class="chatbot-menu-btn" onclick="handlePatientAction('followup', 'Followup')">Followup</button>
                <button class="chatbot-back-btn full-width" onclick="goBackToPatientList()">Back</button>
            </div>
        `);
    }

    function renderPatientSubMenu(type) {
        botReply(`
            <div class="chatbot-grid">
                <button class="chatbot-menu-btn" onclick="fetchData('${type}', 'today', 'Today', ${selectedPatientId})">Today</button>
                <button class="chatbot-menu-btn" onclick="fetchData('${type}', 'week', 'This Week', ${selectedPatientId})">This Week</button>
                <button class="chatbot-menu-btn" onclick="fetchData('${type}', 'month', 'This Month', ${selectedPatientId})">This Month</button>
                <button class="chatbot-back-btn full-width" onclick="goBackToPatientActions()">Back</button>
            </div>
        `);
    }

    window.handlePatientAction = function(type, label) {
        appendUserMessage(label);
        selectedType = type;
        renderPatientSubMenu(type);
    }

    window.goBackToPatientList = function() {
        renderPatientList();
    }

    window.goBackToPatientActions = function() {
        renderPatientActions();
    }

    window.fetchData = function(type, period, label, patientId = null) {
        appendUserMessage(label);
        const loadingId = appendBotMessage('<div style="text-align:center; padding:10px;"><i class="fas fa-spinner fa-spin"></i> Fetching data...</div>');
        const branchId = localStorage.getItem("selectedBranchId");

        $.get('/chatbot/data', { 
            type: type, 
            period: period, 
            branch_id: branchId,
            patient_id: patientId || selectedPatientId 
        }, function(data) {
            if (data.length === 0) {
                $(`#${loadingId}`).html('No data found for ' + label + '.');
            } else {
                let html = `<div><strong>${label} ${type} data:</strong></div><hr>`;
                html += data.map(item => `
                    <div class="chatbot-data-item">
                        <strong>Patient:</strong> ${item.patient_name}<br>
                        <strong>Date:</strong> ${item.date}<br>
                        ${item.doctor_name ? `<strong>Doctor:</strong> ${item.doctor_name}<br>` : ''}
                        ${item.treatment_name ? `<strong>Treatment:</strong> ${item.treatment_name}<br>` : ''}
                        ${item.amount ? `<strong>Amount:</strong> Rs. ${item.amount}<br>` : ''}
                        ${item.status ? `<strong>Status:</strong> ${item.status}<br>` : ''}
                    </div>
                `).join('');
                $(`#${loadingId}`).html(html);
            }
            const pId = patientId || selectedPatientId || 'null';
            appendBotMessage(`<button class="chatbot-back-btn full-width" onclick="goBackToMenu('${type}', ${pId})">Back</button>`);
        });
    }

    window.goBackToMenu = function(type, patientId) {
        if (patientId && patientId !== 'null') {
            selectedPatientId = patientId;
            renderPatientSubMenu(type);
        } else {
            renderSubMenu(type);
        }
    }

    window.goBack = function() {
        renderMain();
    }

    $('#chatbotToggle').click(function() {
        $('#chatbotBox').toggle();
        if ($('#chatbotBox').is(':visible') && chatbotBody.children().length === 0) {
            renderMain();
        }
    });

    $('#chatbotClose').click(function() {
        $('#chatbotBox').hide();
        chatbotBody.empty();
    });
});
</script>
