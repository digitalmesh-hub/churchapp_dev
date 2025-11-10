(function () {
    var initializing = false,
    // Determine if functions can be serialized
    fnTest = /xyz/.test(function () { xyz; }) ? /\b_super\b/ : /.*/;
    // Create a new Class that inherits from this class
    Object.extend = function (prop) {
        var _super = this.prototype;
        // Instantiate a base class (but only create the instance,
        // don't run the init constructor)
        initializing = true;
        var proto = new this();
        initializing = false;
        // Copy the properties over onto the new prototype
        for (var name in prop) {
            // Check if we're overwriting an existing function
            proto[name] = typeof prop[name] == "function" &&
            typeof _super[name] == "function" && fnTest.test(prop[name]) ?
                            (function (name, fn) {
                                return function () {
                                    var tmp = this._super;
                                    // Add a new ._super() method that is the same method
                                    // but on the super-class
                                    this._super = _super[name];
                                    // The method only need to be bound temporarily, so we
                                    // remove it when we're done executing
                                    var ret = fn.apply(this, arguments);
                                    this._super = tmp;
                                    return ret;
                                };
                            })(name, prop[name]) : prop[name];
        }
        // The dummy class constructor
        function Class() {
            // All construction is actually done in the init method
            if (!initializing && this.init)
                this.init.apply(this, arguments);
        }
        // Populate our constructed prototype object
        Class.prototype = proto;
        // Enforce the constructor to be what we expect
        Class.constructor = Class;
        // And make this class extendable
        Class.extend = arguments.callee;
        return Class;
    };
})();



(function () {
	
	Remember = {};
    jsFramework = { lib: { core: {}, ui: {}} };

    //all utility functions must go here
    var utils = Object.extend({
        // constructor
        init: function () {

        },
        // private members

        // public members

        registerNamespace: function (nameSpace, targetNameSpace) {
   
            // this methods will register all project specific name spaces
            if (!nameSpace) {
                throw new Error("jsFramework.lib.core.registerNamespace() =>  nameSpace parameter is required");
            }

            // Check nameSpace doesn't begin or end with a period or contain two periods in a row
            if (nameSpace.charAt(0) === '.' || nameSpace.charAt(nameSpace.length - 1) === '.' || nameSpace.indexOf("..") !== -1) {
                throw new Error("jsFramework.core.registerNamespace() => invalid nameSpace name - " + nameSpace);
            }
            // Break the name at periods and create an array of levels (the object hierarchy)
            var nameSpacelevels = nameSpace.split('.');
           
            var nameSpaceContainer = targetNameSpace == "jsFramework" ? jsFramework : Remember;
            
            if (targetNameSpace != "jsFramework" && nameSpacelevels[0] != "Remember") {
                throw new Error("jsFramework.core.registerNamespace() => nameSpace name should start with 'Remember' - " + nameSpace);
            }

            for (var i = 0; i < nameSpacelevels.length; i++) {
                var nameSpacelevel = nameSpacelevels[i];
                if (nameSpacelevel != "Remember") {
                    // If there is no namespace in the container with this name, create simple empty object.
                    if (!nameSpaceContainer[nameSpacelevel]) {
                        nameSpaceContainer[nameSpacelevel] = {};
                    }
                    nameSpaceContainer = nameSpaceContainer[nameSpacelevel];
                }
            }
          
            // return final namespace object 
            return nameSpaceContainer;
        }
    });
    jsFramework.lib.core.utils = new utils();

    //this class will store the pageBuilders
    var pageBinder = Object.extend({
        // variables/constants
        _pageBuilders: [],

        // constructor
        init: function () {

        },
        // private members

        // public members
        addPageBuilder: function (pageBuilder) {
        	
            // add 
            this._pageBuilders[this._pageBuilders.length] = pageBuilder;
            return pageBuilder;
        },

        buildPage: function () {
            for (var i = 0; i < this._pageBuilders.length; i++) {
                this._pageBuilders[i].buildPage();
            }
        }

    });
    jsFramework.lib.ui.pageBinder = new pageBinder();

})(this);

// this class must be used as the base class for pageBuilders and builPage method should be overridden
jsFramework.lib.ui.basePageBuilder = Object.extend({
	
    // variables/constants
    _settings: {},

    // constructor
    init: function (settings) {
        this._settings = settings;
    },
    // private members

    // public members
    buildPage: function () {
        throw Error("pageBuilder.buildPage must be overriden in sub classes");
        return;
    }
});

// setup
jQuery(document).ready(function () {
    jQuery(document).trigger("SETUP_PAGE_BUILDERS_EVENT");
    jQuery(document).trigger("ON_READY_EVENT");
});

jQuery(document).bind("ON_READY_EVENT", function () {

    jsFramework.lib.ui.pageBinder.buildPage();
	
});