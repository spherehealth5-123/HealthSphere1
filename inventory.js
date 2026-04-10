document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('addItemModal');
    const addBtn = document.getElementById('addItemBtn');
    const closeBtn = document.querySelector('.close-button');
    const addItemForm = document.getElementById('addItemForm');

    // Check if elements exist to avoid "null" errors
    if (!addBtn || !modal) {
        console.error("Critical UI elements missing. Check your HTML IDs.");
        return;
    }

    // --- 1. INITIAL LOAD ---
    fetchInventory();

    // --- 2. MODAL CONTROLS ---
    addBtn.onclick = () => modal.style.display = "block";
    if (closeBtn) closeBtn.onclick = () => modal.style.display = "none";
    
    window.onclick = (e) => { 
        if (e.target == modal) modal.style.display = "none"; 
    }

    // --- 3. SUBMIT FORM ---
    addItemForm.addEventListener('submit', function(e) {
        e.preventDefault(); 

        const formData = new FormData(addItemForm);
        const payload = {
            productName: formData.get('productName'),
            itemNo: formData.get('itemNo'),
            manufacturer: formData.get('manufacturer'),
            category: formData.get('category'),
            price: parseFloat(formData.get('price')), // Convert to number
            quantity: parseInt(formData.get('quantity')), // Convert to integer
            expiryDate: formData.get('expiryDate')
        };

        fetch('php/inventory.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(res => {
            if (!res.ok) throw new Error('Network response was not ok');
            return res.json();
        })
        .then(result => {
            if (result.success) {
                modal.style.display = "none";
                addItemForm.reset();
                fetchInventory(); 
            } else {
                alert("Database Error: " + result.error);
            }
        })
        .catch(err => {
            alert("Fetch Error: Check if php/inventory.php exists and has no PHP errors.");
            console.error(err);
        });
    });

    // --- 4. RENDER TABLE ---
    function fetchInventory() {
        const body = document.getElementById('inventoryBody');
        if (!body) return;

        fetch('php/inventory.php')
            .then(res => res.json())
            .then(data => {
                body.innerHTML = "";
                let critical = 0;

                data.forEach(item => {
                    const isLow = parseInt(item.quantity) < 10;
                    if (isLow) critical++;
                    
                    body.innerHTML += `
                        <tr>
                            <td><strong>${item.product_name}</strong></td>
                            <td>${item.item_no}</td>
                            <td>${item.manufacturer}</td>
                            <td>${item.category}</td>
                            <td>$${parseFloat(item.price).toFixed(2)}</td>
                            <td><span class="status-pill ${isLow ? 'low' : 'ok'}">${item.quantity} Units</span></td>
                            <td>${item.expiry_date}</td>
                            <td><span class="material-symbols-outlined delete-icon" style="cursor:pointer">delete</span></td>
                        </tr>`;
                });

                // Update Stats
                const totalDisplay = document.getElementById('totalItemsDisplay');
                const badgeDisplay = document.getElementById('itemCountBadge');
                const criticalDisplay = document.getElementById('criticalCountDisplay');

                if (totalDisplay) totalDisplay.innerText = data.length;
                if (badgeDisplay) badgeDisplay.innerText = `${data.length} Items`;
                if (criticalDisplay) criticalDisplay.innerText = critical;
            })
            .catch(err => console.error("Error loading table:", err));
    }
});