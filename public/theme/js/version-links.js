/**
 * Version Links Manager
 * Manages multiple file links with version names for items
 */

(function() {
    'use strict';

    var VersionLinksManager = {
        initialized: false,
        versionCounter: 1,

        init: function() {
            // Prevent double initialization
            if (this.initialized) {
                return;
            }

            // Bind add button click event
            var addBtn = document.getElementById('add-version-link-btn');
            if (addBtn) {
                addBtn.addEventListener('click', this.addVersionBlock.bind(this));
            }

            // Initialize event listeners for existing remove buttons
            this.bindRemoveButtons();

            // Set initialized flag
            this.initialized = true;

            // Set initial counter based on existing blocks
            var existingBlocks = document.querySelectorAll('.version-link-block');
            if (existingBlocks.length > 0) {
                this.versionCounter = existingBlocks.length;
            }
        },

        addVersionBlock: function(e) {
            if (e) {
                e.preventDefault();
            }

            var container = document.getElementById('version-links-container');
            if (!container) {
                return;
            }

            this.versionCounter++;

            var blockHtml = `
                <div class="version-link-block" style="margin-bottom: 15px; padding: 15px; border: 1px solid #e0e0e0; border-radius: 4px; background-color: #f9f9f9;">
                    <div class="row">
                        <div class="col-sm-5">
                            <div class="form-group">
                                <input type="text" name="version_names[]" class="form-control" placeholder="Version name" style="font-size: 14px;">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <input type="text" name="version_links[]" class="form-control" placeholder="File link / URL" data-bvalidator="url" style="font-size: 14px;">
                            </div>
                        </div>
                        <div class="col-sm-1" style="text-align: center;">
                            <button type="button" class="btn btn-danger btn-sm remove-version-btn" style="margin-top: 0px; padding: 6px 10px; font-size: 18px;" title="Remove this version">
                                🗑️
                            </button>
                        </div>
                    </div>
                </div>
            `;

            container.insertAdjacentHTML('beforeend', blockHtml);

            // Bind remove button for the newly added block
            this.bindRemoveButtons();
        },

        bindRemoveButtons: function() {
            var removeButtons = document.querySelectorAll('.remove-version-btn');
            removeButtons.forEach(function(btn) {
                // Remove existing event listener to prevent duplicates
                btn.removeEventListener('click', this.removeVersionBlock);
                // Add event listener
                btn.addEventListener('click', this.removeVersionBlock.bind(this));
            }.bind(this));
        },

        removeVersionBlock: function(e) {
            if (e) {
                e.preventDefault();
            }

            var block = e.target.closest('.version-link-block');
            if (block) {
                // Check if there's at least one block remaining
                var container = document.getElementById('version-links-container');
                var blocks = container.querySelectorAll('.version-link-block');
                
                if (blocks.length > 1) {
                    block.remove();
                } else {
                    // If it's the last block, just clear the values
                    var inputs = block.querySelectorAll('input');
                    inputs.forEach(function(input) {
                        input.value = '';
                    });
                }
            }
        },

        // Save current state of version links
        saveState: function() {
            var versionData = [];
            var blocks = document.querySelectorAll('.version-link-block');
            
            blocks.forEach(function(block) {
                var nameInput = block.querySelector('input[name="version_names[]"]');
                var linkInput = block.querySelector('input[name="version_links[]"]');
                
                if (nameInput && linkInput) {
                    versionData.push({
                        name: nameInput.value,
                        link: linkInput.value
                    });
                }
            });

            return versionData;
        },

        // Restore state from saved data
        restoreState: function(versionData) {
            if (!versionData || versionData.length === 0) {
                return;
            }

            var container = document.getElementById('version-links-container');
            if (!container) {
                return;
            }

            // Clear existing blocks
            container.innerHTML = '';

            // Recreate blocks with saved data
            versionData.forEach(function(data, index) {
                var blockHtml = `
                    <div class="version-link-block" style="margin-bottom: 15px; padding: 15px; border: 1px solid #e0e0e0; border-radius: 4px; background-color: #f9f9f9;">
                        <div class="row">
                            <div class="col-sm-5">
                                <div class="form-group">
                                    <input type="text" name="version_names[]" class="form-control" placeholder="Version name" value="${this.escapeHtml(data.name)}" style="font-size: 14px;">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <input type="text" name="version_links[]" class="form-control" placeholder="File link / URL" value="${this.escapeHtml(data.link)}" data-bvalidator="url" style="font-size: 14px;">
                                </div>
                            </div>
                            <div class="col-sm-1" style="text-align: center;">
                                <button type="button" class="btn btn-danger btn-sm remove-version-btn" style="margin-top: 0px; padding: 6px 10px; font-size: 18px;" title="Remove this version">
                                    🗑️
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                container.insertAdjacentHTML('beforeend', blockHtml);
            }.bind(this));

            // Rebind remove buttons
            this.bindRemoveButtons();
        },

        // Helper function to escape HTML
        escapeHtml: function(text) {
            if (!text) return '';
            var map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        },

        // Reinitialize after DOM changes
        reinit: function() {
            this.initialized = false;
            this.init();
        }
    };

    // Expose to global scope
    window.VersionLinksManager = VersionLinksManager;

    // Auto-initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            VersionLinksManager.init();
        });
    } else {
        VersionLinksManager.init();
    }

})();

