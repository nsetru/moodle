/**
 * A dialogue type designed to display a confirmation to the user.
 *
 * @module moodle-core-notification
 * @submodule moodle-core-notification-confirm
 */

var CONFIRM_NAME = 'Moodle confirmation dialogue',
    CONFIRM;

/**
 * Extends core Dialogue to show the confirmation dialogue.
 *
 * @param {Object} config Object literal specifying the dialogue configuration properties.
 * @constructor
 * @class M.core.confirm
 * @extends M.core.dialogue
 */
CONFIRM = function(config) {
    CONFIRM.superclass.constructor.apply(this, [config]);
};
Y.extend(CONFIRM, M.core.dialogue, {
    /**
     * The list of events to detach when destroying this dialogue.
     *
     * @property _closeEvents
     * @type EventHandle[]
     * @private
     */
    _closeEvents: null,
    initializer: function() {
        this._closeEvents = [];
        this.publish('complete');
        this.publish('complete-yes');
        this.publish('complete-no');
        var yes = Y.Node.create('<input type="button" id="id_yuiconfirmyes-' + this.get('COUNT') + '" value="'+this.get(CONFIRMYES)+'" />'),
            no = Y.Node.create('<input type="button" id="id_yuiconfirmno-' + this.get('COUNT') + '" value="'+this.get(CONFIRMNO)+'" />'),
            content = Y.Node.create('<div class="confirmation-dialogue"></div>')
                        .append(Y.Node.create('<div class="confirmation-message">'+this.get(QUESTION)+'</div>'))
                        .append(Y.Node.create('<div class="confirmation-buttons"></div>')
                            .append(yes)
                            .append(no));
        this.get(BASE).addClass('moodle-dialogue-confirm');
        this.setStdModContent(Y.WidgetStdMod.BODY, content, Y.WidgetStdMod.REPLACE);
        this.setStdModContent(Y.WidgetStdMod.HEADER,
                '<h1 id="moodle-dialogue-'+this.get('COUNT')+'-header-text">' + this.get(TITLE) + '</h1>', Y.WidgetStdMod.REPLACE);
        this.after('destroyedChange', function(){this.get(BASE).remove();}, this);

        this._closeEvents.push(
            Y.on('key', this.submit, window, 'down:27', this, false),
            yes.on('click', this.submit, this, true),
            no.on('click', this.submit, this, false)
        );

        var closeButton = this.get('boundingBox').one('.closebutton');
        if (closeButton) {
            // The close button should act exactly like the 'No' button.
            this._closeEvents.push(
                closeButton.on('click', this.submit, this)
            );
        }
    },
    submit: function(e, outcome) {
        new Y.EventHandle(this._closeEvents).detach();
        this.fire('complete', outcome);
        if (outcome) {
            this.fire('complete-yes');
        } else {
            this.fire('complete-no');
        }
        this.hide();
        this.destroy();
    }
}, {
    NAME: CONFIRM_NAME,
    CSS_PREFIX: DIALOGUE_PREFIX,
    ATTRS: {

        /**
         * The button text to use to accept the confirmation.
         *
         * @attribute yesLabel
         * @type String
         * @default 'Yes'
         */
        yesLabel: {
            validator: Y.Lang.isString,
            value: M.util.get_string('yes', 'moodle')
        },

        /**
         * The button text to use to reject the confirmation.
         *
         * @attribute noLabel
         * @type String
         * @default 'No'
         */
        noLabel: {
            validator: Y.Lang.isString,
            value: M.util.get_string('no', 'moodle')
        },

        /**
         * The title of the dialogue.
         *
         * @attribute title
         * @type String
         * @default 'Confirm'
         */
        title: {
            validator: Y.Lang.isString,
            value: M.util.get_string('confirm', 'moodle')
        },

        /**
         * The question posed by the dialogue.
         *
         * @attribute question
         * @type String
         * @default 'Are you sure?'
         */
        question: {
            validator: Y.Lang.isString,
            value: M.util.get_string('areyousure', 'moodle')
        }
    }
});
Y.augment(CONFIRM, Y.EventTarget);

M.core.confirm = CONFIRM;
