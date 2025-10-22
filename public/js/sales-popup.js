/**
 * Live Sales Popup Notification System
 * Shows recent or simulated purchases to boost conversions
 */

(function($) {
    'use strict';
    
    // Debug log
    console.log('🚀 Sales Popup Script Loaded');
    console.log('jQuery available:', typeof $ !== 'undefined');

    // Configuration
    const config = {
        apiUrl: 'api/sales-notifications', // Relative URL (works with subdirectories)
        minInterval: 10000,  // 10 seconds
        maxInterval: 20000,  // 20 seconds
        displayDuration: 5000, // 5 seconds to display each popup
        animationDuration: 500, // Animation duration in ms
        maxNotifications: 20, // Max notifications to cycle through
        position: 'bottom-left' // 'bottom-left' or 'bottom-right'
    };

    // Notification data storage
    let notifications = [];
    let currentIndex = 0;
    let popupTimeout = null;
    let nextPopupTimeout = null;

    /**
     * Initialize the sales popup system
     */
    function init() {
        console.log('🎯 Initializing sales popup system...');
        
        // Create popup container if it doesn't exist
        if ($('#sales-popup-container').length === 0) {
            createPopupContainer();
            console.log('✅ Popup container created');
        } else {
            console.log('⚠️ Popup container already exists');
        }

        // Fetch notifications data
        fetchNotifications();
    }

    /**
     * Create the popup container element
     */
    function createPopupContainer() {
        const positionClass = config.position === 'bottom-right' ? 'sales-popup-right' : 'sales-popup-left';
        
        const popupHtml = `
            <div id="sales-popup-container" class="sales-popup-container ${positionClass}">
                <div class="sales-popup-inner">
                    <div class="sales-popup-icon">
                        💡
                    </div>
                    <div class="sales-popup-content">
                        <div class="sales-popup-text"></div>
                        <div class="sales-popup-time"></div>
                    </div>
                    <button class="sales-popup-close" aria-label="Close">&times;</button>
                </div>
            </div>
        `;
        
        $('body').append(popupHtml);

        // Bind close button
        $('.sales-popup-close').on('click', function() {
            hidePopup();
        });
    }

    /**
     * Fetch notifications from the API
     */
    function fetchNotifications() {
        console.log('📡 Fetching notifications from:', config.apiUrl);
        
        $.ajax({
            url: config.apiUrl,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                console.log('✅ API Response:', response);
                
                if (response.success && response.notifications.length > 0) {
                    notifications = response.notifications;
                    currentIndex = 0;
                    console.log('📦 Loaded', notifications.length, 'notifications');
                    
                    // Start showing popups
                    scheduleNextPopup();
                } else {
                    console.log('⚠️ No sales notifications available');
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ Error fetching sales notifications:', error);
                console.error('Status:', status, 'XHR:', xhr);
                // Retry after 30 seconds if failed
                setTimeout(fetchNotifications, 30000);
            }
        });
    }

    /**
     * Schedule the next popup to appear
     */
    function scheduleNextPopup() {
        // Clear any existing timeout
        if (nextPopupTimeout) {
            clearTimeout(nextPopupTimeout);
        }

        // Random interval between min and max
        const interval = Math.floor(Math.random() * (config.maxInterval - config.minInterval + 1)) + config.minInterval;
        
        nextPopupTimeout = setTimeout(function() {
            showNextPopup();
        }, interval);
    }

    /**
     * Show the next popup in the queue
     */
    function showNextPopup() {
        if (notifications.length === 0) {
            return;
        }

        // Get the current notification
        const notification = notifications[currentIndex];
        
        // Update popup content
        updatePopupContent(notification);
        
        // Show popup
        showPopup();
        
        // Move to next notification (cycle through)
        currentIndex = (currentIndex + 1) % notifications.length;
        
        // If we've cycled through all notifications, refresh data
        if (currentIndex === 0) {
            setTimeout(fetchNotifications, 60000); // Refresh every minute when cycle completes
        }
        
        // Schedule popup to hide
        popupTimeout = setTimeout(function() {
            hidePopup();
            // Schedule next popup
            scheduleNextPopup();
        }, config.displayDuration);
    }

    /**
     * Update popup content with notification data
     */
    function updatePopupContent(notification) {
        const $popup = $('#sales-popup-container');
        const message = `Someone from <strong>${notification.location}</strong> just purchased <strong>${notification.product_name}</strong>!`;
        
        $popup.find('.sales-popup-text').html(message);
        
        // Hide time display (removed as per user request)
        $popup.find('.sales-popup-time').text('');
    }

    /**
     * Show the popup with animation
     */
    function showPopup() {
        const $popup = $('#sales-popup-container');
        $popup.addClass('sales-popup-show');
    }

    /**
     * Hide the popup with animation
     */
    function hidePopup() {
        const $popup = $('#sales-popup-container');
        $popup.removeClass('sales-popup-show');
        
        // Clear popup timeout
        if (popupTimeout) {
            clearTimeout(popupTimeout);
        }
    }

    /**
     * Get time ago string from timestamp
     */
    function getTimeAgo(timestamp) {
        const now = new Date();
        const then = new Date(timestamp);
        const diffInSeconds = Math.floor((now - then) / 1000);
        
        if (diffInSeconds < 60) {
            return 'Just now';
        } else if (diffInSeconds < 3600) {
            const minutes = Math.floor(diffInSeconds / 60);
            return minutes + (minutes === 1 ? ' minute ago' : ' minutes ago');
        } else if (diffInSeconds < 86400) {
            const hours = Math.floor(diffInSeconds / 3600);
            return hours + (hours === 1 ? ' hour ago' : ' hours ago');
        } else {
            const days = Math.floor(diffInSeconds / 86400);
            return days + (days === 1 ? ' day ago' : ' days ago');
        }
    }

    // Initialize when document is ready
    $(document).ready(function() {
        console.log('📄 Document ready, sales popup will initialize in 3 seconds...');
        
        // Small delay before starting to avoid interfering with page load
        setTimeout(function() {
            console.log('⏰ Starting sales popup initialization now');
            init();
        }, 3000);
    });
    
    console.log('✅ Sales Popup Event Handler Registered');

})(jQuery);

