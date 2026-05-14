/**
 * Committee Dependant Support - JavaScript Handler
 * Add this to backend/assets/theme/js/Remember.committeeAddDependant.ui.js
 * 
 * Handles dependant search, selection, and assignment to committees
 */

$(document).ready(function() {

    // Search for dependants
    $(document).on('click', '.add-dependant', function(){
        var dependantName = $('#dependant-search-name').val().trim();
        
        if(dependantName == '' || dependantName.length < 2){
            alert('Please enter at least 2 characters to search');
            return;
        }
        
        var ajaxUrl = $('#homeUrl').val() + $('#admin-get-dependant-for-search-Url').val();
        
        $.get(ajaxUrl, {
            dependantName: dependantName
        }, function (res) {
            if (typeof (res) != 'undefined' && res.status == 'success') {
                displayDependantResults(res.data);
            } else {
                $('#dependantResultsList').html('<p class="text-danger">No dependants found</p>');
                $('#dependantSearchResults').show();
            }
        });
    });
    
    // Display search results
    function displayDependantResults(dependants) {
        if(dependants.length == 0) {
            $('#dependantResultsList').html('<div class="alert alert-warning" style="margin: 20px;"><i class="glyphicon glyphicon-exclamation-sign"></i> No dependants found matching your search.</div>');
            $('#dependantSearchResults').show();
            return;
        }
        
        var html = '<div class="list-group" style="margin: 0;">';
        dependants.forEach(function(dep) {
            html += '<a href="javascript:void(0)" class="list-group-item dependant-result-item" data-dependantid="' + dep.dependantid + '" style="margin-bottom: 10px; border: 2px solid #ddd; border-radius: 5px;">';
            html += '<div class="row">';
            html += '<div class="col-md-9">';
            html += '<h4 class="list-group-item-heading" style="margin-top: 0; color: #2c3e50;">';
            html += '<i class="glyphicon glyphicon-user" style="color: #3498db;"></i> ';
            html += '<strong>' + (dep.title || '') + ' ' + dep.name + '</strong>';
            html += ' <span class="label label-info" style="font-size: 12px; margin-left: 8px;">' + dep.relation + '</span>';
            html += '</h4>';
            html += '<p class="list-group-item-text" style="font-size: 14px; margin-bottom: 5px;">';
            html += '<i class="glyphicon glyphicon-user" style="color: #27ae60;"></i> <strong>Parent:</strong> ' + dep.parent_name;
            html += '</p>';
            html += '<p class="list-group-item-text" style="font-size: 13px; color: #7f8c8d;">';
            html += '<i class="glyphicon glyphicon-tag"></i> Member #' + dep.parent_memberno;
            html += '</p>';
            html += '</div>';
            html += '<div class="col-md-3 text-right" style="display: flex; align-items: center; justify-content: flex-end;">';
            html += '<button class="btn btn-success btn-lg select-dependant-btn" data-dependantid="' + dep.dependantid + '" style="font-size: 16px;">';
            html += '<i class="glyphicon glyphicon-hand-right"></i> SELECT THIS';
            html += '</button>';
            html += '</div>';
            html += '</div>';
            html += '</a>';
        });
        html += '</div>';
        
        $('#dependantResultsList').html(html);
        $('#dependantSearchResults').show();
    }
    
    // Select dependant from results
    $(document).on('click', '.select-dependant-btn, .dependant-result-item', function(e){
        e.preventDefault();
        var dependantId = $(this).data('dependantid');
        
        if(!dependantId) {
            return;
        }
        
        // Show loading message
        $('#CommitteeDependantDetailsDiv').html(
            '<div class="alert alert-info text-center" style="margin: 20px; font-size: 16px;">' +
            '<i class="glyphicon glyphicon-refresh glyphicon-spin"></i> ' +
            '<strong>Loading dependant details...</strong>' +
            '</div>'
        );
        
        // Hide search results
        $('#dependantSearchResults').hide();
        
        // Load dependant details
        loadDependantDetails(dependantId);
    });
    
    // Load dependant details for committee assignment
    function loadDependantDetails(dependantId) {
        var ajaxUrl = $('#homeUrl').val() + $('#admin-get-dependant-details-Url').val();
        
        $.get(ajaxUrl, {
            dependantId: dependantId
        }, function (res) {
            if (typeof (res) != 'undefined' && res.status == 'success') {
                $('#CommitteeDependantDetailsDiv').html(res.data);
                // Load committee periods when committee type changes
                bindCommitteePeriodChange('Dep');
            } else {
                alert('Error loading dependant details');
            }
        });
    }
    
    // Bind committee type change to load periods
    function bindCommitteePeriodChange(suffix) {
        $(document).on('change', '#committeeTypeId' + suffix, function(){
            var committeeTypeId = $(this).val();
            if(committeeTypeId) {
                loadCommitteePeriods(committeeTypeId, suffix);
            }
        });
    }
    
    // Load committee periods based on committee type
    function loadCommitteePeriods(committeeTypeId, suffix) {
        var ajaxUrl = $('#homeUrl').val() + $('#admin-get-period-by-type-Url').val();
        
        $.get(ajaxUrl, {
            committeeTypeId: committeeTypeId
        }, function (res) {
            if (typeof (res) != 'undefined' && res.status == 'success') {
                $('#periodType' + suffix).html(res.data);
            }
        });
    }
    
    // Save dependant to committee
    $(document).on('click', '.saveCommitteeDependant', function(){
        var button = $(this);
        var institutionId = button.data('institutionid');
        var memberId = button.data('memberid');  // Parent member ID
        var userId = button.data('userid');      // Parent's user ID
        var dependantId = button.data('dependantid');
        var isSpouse = 'd';  // Mark as dependant
        
        var committeeTypeId = $('#committeeTypeIdDep').val();
        var designationId = $('#designationTypeDep').val();
        var committeePeriodId = $('#periodTypeDep').val();
        
        // Validation
        if(!committeeTypeId || committeeTypeId == 'Please Select') {
            $('#errorMessageDivDep').html('<strong>Please select a committee</strong>');
            $('#errorDivDep').show();
            return;
        }
        
        if(!designationId || designationId == 'Please Select') {
            $('#errorMessageDivDep').html('<strong>Please select a designation</strong>');
            $('#errorDivDep').show();
            return;
        }
        
        if(!committeePeriodId || committeePeriodId == 'Please Select') {
            $('#errorMessageDivDep').html('<strong>Please select a period</strong>');
            $('#errorDivDep').show();
            return;
        }
        
        // Hide error div
        $('#errorDivDep').hide();
        
        // Disable button
        button.prop('disabled', true).html('<i class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></i> Saving...');
        
        var ajaxUrl = $('#homeUrl').val() + $('#admin-save-committee-member-Url').val();
        
        $.post(ajaxUrl, {
            institutionId: institutionId,
            memberId: memberId,
            userId: userId,
            dependantId: dependantId,
            committeeGroupId: committeeTypeId,
            designationId: designationId,
            committeePeriodId: committeePeriodId,
            isSpouse: isSpouse
        }, function (res) {
            button.prop('disabled', false).html('Save');
            
            if (res.status == 'success') {
                $('#SuccessMessageDivDep .message').html('<strong>Dependant successfully added to committee!</strong>');
                $('#SuccessMessageDivDep').show();
                
                // Reset form after 2 seconds
                setTimeout(function(){
                    $('#SuccessMessageDivDep').hide();
                    $('#CommitteeDependantDetailsDiv').html('');
                    $('#dependant-search-name').val('');
                }, 2000);
            } else {
                $('#errorMessageDivDep').html('<strong>Error: ' + (res.data || 'Could not add dependant to committee') + '</strong>');
                $('#errorDivDep').show();
            }
        }).fail(function(){
            button.prop('disabled', false).html('Save');
            $('#errorMessageDivDep').html('<strong>Network error. Please try again.</strong>');
            $('#errorDivDep').show();
        });
    });
    
    // Clear search on Enter key
    $('#dependant-search-name').keypress(function(e){
        if(e.which == 13) {
            e.preventDefault();
            $('.add-dependant').click();
        }
    });
    
});
