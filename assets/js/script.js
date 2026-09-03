// Custom JavaScript Functions

// Show alert notification
function showAlert(type, message) {
    const alertClass = {
        'success': 'alert-success',
        'error': 'alert-danger',
        'warning': 'alert-warning',
        'info': 'alert-info'
    };

    const alertHtml = `
        <div class="alert ${alertClass[type] || 'alert-info'} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;

    // Insert at top of main content
    $('main').prepend(alertHtml);

    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        $('.alert').fadeOut('slow', function() {
            $(this).remove();
        });
    }, 5000);
}

// Add to cart
function addToCart(foodId, foodName, price) {
    $.ajax({
        url: 'api/add-to-cart.php',
        method: 'POST',
        data: {
            food_id: foodId,
            food_name: foodName,
            price: price,
            quantity: 1
        },
        success: function(response) {
            const data = JSON.parse(response);
            if (data.success) {
                showAlert('success', 'Item added to cart!');
                updateCartCount();
            } else {
                showAlert('error', data.message || 'Failed to add item');
            }
        },
        error: function() {
            showAlert('error', 'Error adding to cart');
        }
    });
}

// Update cart count
function updateCartCount() {
    $.ajax({
        url: 'api/get-cart-count.php',
        method: 'GET',
        success: function(response) {
            const count = parseInt(response);
            const badge = $('.cart-badge');
            if (count > 0) {
                if (badge.length === 0) {
                    $('.navbar-nav .position-relative').append(`<span class="cart-badge">${count}</span>`);
                } else {
                    badge.text(count);
                }
            } else {
                badge.remove();
            }
        }
    });
}

// Remove from cart
function removeFromCart(cartKey) {
    if (confirm('Are you sure you want to remove this item?')) {
        $.ajax({
            url: 'api/remove-from-cart.php',
            method: 'POST',
            data: {
                cart_key: cartKey
            },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    showAlert('success', 'Item removed from cart!');
                    location.reload();
                } else {
                    showAlert('error', data.message || 'Failed to remove item');
                }
            },
            error: function() {
                showAlert('error', 'Error removing from cart');
            }
        });
    }
}

// Update order status
function updateOrderStatus(orderId, newStatus) {
    $.ajax({
        url: 'api/update-order-status.php',
        method: 'POST',
        data: {
            order_id: orderId,
            status: newStatus
        },
        success: function(response) {
            const data = JSON.parse(response);
            if (data.success) {
                showAlert('success', 'Order status updated!');
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                showAlert('error', data.message || 'Failed to update status');
            }
        },
        error: function() {
            showAlert('error', 'Error updating order status');
        }
    });
}

// Delete item (for admin)
function deleteItem(type, itemId) {
    if (confirm(`Are you sure you want to delete this ${type}?`)) {
        window.location.href = `?action=delete&id=${itemId}`;
    }
}

// Toggle item status
function toggleStatus(type, itemId, currentStatus) {
    const newStatus = currentStatus === 1 ? 0 : 1;
    window.location.href = `?action=toggle&id=${itemId}&status=${newStatus}`;
}

// Format currency
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'NPR'
    }).format(amount);
}

// Print invoice
function printInvoice(orderId) {
    window.open(`invoice.php?id=${orderId}`, 'invoice', 'width=800,height=600');
}

// Export to PDF (requires library)
function exportToPDF(elementId, fileName) {
    const element = document.getElementById(elementId);
    const opt = {
        margin: 10,
        filename: fileName,
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { orientation: 'portrait', unit: 'mm', format: 'a4' }
    };
    html2pdf().set(opt).from(element).save();
}

// Validate email
function isValidEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

// Validate phone
function isValidPhone(phone) {
    const regex = /^[0-9]{7,15}$/;
    return regex.test(phone);
}

// Format phone number
function formatPhone(phone) {
    return phone.replace(/(\d{3})(\d{3})(\d{4})/, '($1) $2-$3');
}

// Debounce function for search
function debounce(func, delay) {
    let timeoutId;
    return function(...args) {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => func.apply(this, args), delay);
    };
}

// Live search
const liveSearch = debounce(function(searchTerm) {
    if (searchTerm.length > 2) {
        $.ajax({
            url: 'api/search.php',
            method: 'GET',
            data: { q: searchTerm },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    displaySearchResults(data.results);
                }
            }
        });
    }
}, 300);

// Display search results
function displaySearchResults(results) {
    let html = '<div class="search-results">';
    
    if (results.length === 0) {
        html += '<p class="text-center text-muted">No results found</p>';
    } else {
        results.forEach(item => {
            html += `
                <div class="search-result-item">
                    <a href="menu.php?category=${item.category_id}">
                        ${item.name} - Rs. ${item.price}
                    </a>
                </div>
            `;
        });
    }
    
    html += '</div>';
    $('#searchResults').html(html);
}

// Initialize tooltips and popovers
document.addEventListener('DOMContentLoaded', function() {
    // Bootstrap tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Bootstrap popovers
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    // Initialize cart count
    updateCartCount();
});

// Smooth scroll
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth'
            });
        }
    });
});
