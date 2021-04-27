<script type="text/javascript" src="{{ asset('js/app.js') }}"></script>

<script type='text/javascript'>
    function handleApp() {
        const elem = document.getElementById('range');
        const dnd = document.getElementById('Gov-Client-Browser-Do-Not-Track');
        const userAgent = document.getElementById('Gov-Client-Browser-JS-User-Agent');
        const browserPlugins = document.getElementById('Gov-Client-Browser-Plugins');
        const deviceScreen = document.getElementById('Gov-Client-Screens');
        const timezone = document.getElementById('Gov-Client-Timezone');
        const windowSize = document.getElementById('Gov-Client-Window-Size');
        const vendorVersion = document.getElementById('Gov-Vendor-Version');

        if(elem) {
            const dateRangePicker = new DateRangePicker(elem, {
                autohide: true,
            });
        }

        // ********************* Collect headers  *******************

        // Browser Do Not Track
        if (window.doNotTrack || navigator.doNotTrack || navigator.msDoNotTrack || 'msTrackingProtectionEnabled' in window.external) {
            if (
                window.doNotTrack == "1" || 
                navigator.doNotTrack == "yes" || 
                navigator.doNotTrack == "1" || 
                navigator.msDoNotTrack == "1" || 
                window.external.msTrackingProtectionEnabled()
            ){
                dnd.value = true;
            } else {
                dnd.value = false;
            }
        } else {
            dnd.value = false;
        }

        // User agent
        userAgent.value = navigator.userAgent;
        
        // Browser plugins
        const plugins = navigator.plugins;
        let pluginsString = '';
        for (var i = 0; i < plugins.length; i++) {
            pluginsString += encodeURIComponent(plugins[i].name);
            if(plugins.length > i+1) {
                pluginsString += ',';
            }
        }
        browserPlugins.value = pluginsString;

        // Device screens
        const screens = `width=${window.screen.width}&height=${window.screen.height}&scaling-factor=${window.devicePixelRatio}&colour-depth=${window.screen.colorDepth}`;
        deviceScreen.value = screens;

        // Client timezone
        const offset = new Date().getTimezoneOffset(); const o = Math.abs(offset);
        const utctz = (offset < 0 ? "+" : "-") + ("00" + Math.floor(o / 60)).slice(-2) + ":" + ("00" + (o % 60)).slice(-2);
        timezone.value = 'UTC'+utctz;

        // Window size
        windowSize.value = `width=${document.documentElement.clientWidth}&height=${document.documentElement.clientHeight}`;

        // Vendor version
        vendorVersion.value = encodeURIComponent('River eSolutions MTD App')+'='+encodeURIComponent('v1.0.0');
    }

    // see if DOM is already available
    if (document.readyState === "complete" || document.readyState === "interactive") {
        // call on next available tick
        setTimeout(handleApp, 1);
    } else {
        document.addEventListener("DOMContentLoaded", handleApp);
    }
</script>