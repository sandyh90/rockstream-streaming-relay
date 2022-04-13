<div class="text-center mb-2">
    <h5 class="fw-light">How To Connect Stream From Local Lan Network</h5>
</div>
<ul>
    <li>Search or use <kbd><kbd><span class="bi bi-windows"></span></kbd> + <kbd>R</kbd></kbd> and type
        <kbd>cmd</kbd>
    </li>
    <li>Type command <kbd>ipconfig</kbd> in running cmd window</li>
    <li>And check what adapter that you use <strong>Wireless LAN</strong> or <strong>Ethernet</strong>
    </li>
    <li>From the list network adapter check <strong>IPv4 Address</strong> section because that your
        local computer IP
    </li>
    <li>Change url stream <code>rtmp://localhost/live</code> to
        <code>rtmp://[Your local computer host IP e.g 192.168.xxx.xxx]/live</code>
    </li>
</ul>
<div class="my-2">
    <img loading="lazy" src=" {{ asset('assets/img/how-to-setup/ipconfig-sample-setup.png') }}" width="100%"
        alt="Get Local Ip">
</div>