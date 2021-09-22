const TnpToast = (function () { //Module pattern mi permette di rendere private le DEFAULT_OPTIONS e funzione di _render
    'use strict';
    const DEFAULT_OPTIONS = {
        duration: 2000,
        position: 'bottom right',
        wrapperPadding: '20px'
    };

    //Constructor function (mi permette di creare uno scope)
    function TnpToast(options) {

        this._options = Object.assign({}, DEFAULT_OPTIONS, options);
        this._mainWrapperElement = null;

        this._render = function (message, type) {

            if (!this._mainWrapperElement) {
                this._createMainWrapper();
            }

            const columnDirection = this._getNotificationColumnDirectionClassName();

            const notificationElement = document.createElement('div');
            notificationElement.className = `notification notification-${type} ${columnDirection}` + ' ' + this._getNotificationShowAnimationClassName();
            notificationElement.append(message);

            this._mainWrapperElement.appendChild(notificationElement);

            setTimeout(() => {
                this._removeNotification(notificationElement)
            }, this._options.duration);

        }

        this._removeNotification = function (notificationElement) {
            notificationElement.className = notificationElement.className + ' ' + this._getNotificationRemoveAnimationClassName();
            setTimeout(() => {
                this._mainWrapperElement.removeChild(notificationElement);
            }, 1000);
        }

        this._createMainWrapper = function () {
            this._mainWrapperElement = document.createElement('div');
            this._mainWrapperElement.className = 'tnp-toast-main-wrapper';
            this._mainWrapperElement.style.padding = this._options.wrapperPadding;

            const alignments = this._getFlexboxAlignments();
            for (let alignmentProperty of Object.keys(alignments)) {
                this._mainWrapperElement.style[alignmentProperty] = alignments[alignmentProperty];
            }

            const columnDirection = this._getNotificationColumnDirectionClassName();
            if (columnDirection === 'top-to-bottom') {
                this._mainWrapperElement.style.flexDirection = 'column-reverse';
            }

            document.body.appendChild(this._mainWrapperElement);
        }

        this._getFlexboxAlignments = function () {
            const position = this._options.position;
            const spatialPositions = position.split(' ');
            const flexAlignments = {}
            for (let pos of spatialPositions) {
                if (pos === 'top') {
                    flexAlignments.justifyContent = 'flex-end'; //poi aggiungo flex-direction: column-reverse;
                } else if (pos === 'bottom') {
                    flexAlignments.justifyContent = 'flex-end';
                } else if (pos === 'left') {
                    flexAlignments.alignItems = 'flex-start';
                } else if (pos === 'right') {
                    flexAlignments.alignItems = 'flex-end';
                }
            }
            return flexAlignments;
        }

        this._getNotificationColumnDirectionClassName = function () {
            const position = this._options.position;

            return position.includes('top') ? 'top-to-bottom' : 'bottom-to-top';
        }

        this._getNotificationShowAnimationClassName = function () {
            const position = this._options.position;

            return position.includes('top') ? 'push-down' : 'push-up';
        }

        this._getNotificationRemoveAnimationClassName = function () {
            const position = this._options.position;

            return position.includes('top') ? 'pop-up' : 'pop-down';
        }

    }

    TnpToast.prototype.error = function (message) {
        this._render(message, 'error');
    }

    TnpToast.prototype.success = function (message) {
        this._render(message, 'success');
    }

    TnpToast.prototype.info = function (message) {
        this._render(message, 'info');
    }

    TnpToast.prototype.warning = function (message) {
        this._render(message, 'warning');
    }

    return TnpToast;

})();

window.TnpToast = TnpToast;

/*
 //ESEMPIO UTILIZZO API TnpToast
 
 const toastTop = new TnpToast({duration: 5000, position: 'bottom right', wrapperPadding: '70px 20px'});
 
 setTimeout(function () {
 toastTop.info('messaggio di info');
 }, 3000);
 
 setTimeout(function () {
 toastTop.error('messaggio di errore');
 }, 5000);
 
 */




