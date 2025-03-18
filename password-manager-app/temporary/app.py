#!/usr/bin/env python3
"""
Enhanced WiFi User Detector - Scans entire network to find all connected devices.
Uses ping sweeping to discover all active devices on the network.
"""

import os
import socket
import subprocess
import threading
import time
import datetime
import re
import argparse
from concurrent.futures import ThreadPoolExecutor

class EnhancedWifiDetector:
    def __init__(self, subnet=None, timeout=1):
        self.subnet = subnet
        self.timeout = timeout
        self.devices = {}  # IP -> {mac, hostname, last_seen}
        self.lock = threading.Lock()

    def get_default_gateway(self):
        """Determine the default gateway and subnet."""
        if os.name == 'nt':  # Windows
            output = subprocess.check_output("ipconfig", universal_newlines=True)
            # Get default gateway
            gateway_match = re.search(r'Default Gateway.*: ([\d.]+)', output)
            gateway = gateway_match.group(1) if gateway_match else None
            
            # Get subnet mask
            ip_match = re.search(r'IPv4 Address.*: ([\d.]+)', output)
            subnet_match = re.search(r'Subnet Mask.*: ([\d.]+)', output)
            
            if gateway and ip_match and subnet_match:
                return gateway
        else:  # Linux/Mac
            try:
                output = subprocess.check_output("ip route | grep default", shell=True, universal_newlines=True)
                return output.split()[2]
            except:
                return None
        return None

    def get_subnet_from_gateway(self, gateway):
        """Get subnet from gateway IP address."""
        if gateway:
            # Assume a standard /24 subnet
            return '.'.join(gateway.split('.')[0:3]) + '.0/24'
        return None

    def ping_ip(self, ip):
        """Ping an IP address to check if it's active."""
        if os.name == 'nt':  # Windows
            ping_cmd = f"ping -n 1 -w {self.timeout * 1000} {ip}"
        else:  # Linux/Mac
            ping_cmd = f"ping -c 1 -W {self.timeout} {ip}"
        
        try:
            output = subprocess.check_output(ping_cmd, shell=True, stderr=subprocess.STDOUT, universal_newlines=True)
            return "TTL=" in output or "ttl=" in output
        except subprocess.CalledProcessError:
            return False

    def get_mac_from_ip(self, ip):
        """Get MAC address for an IP using ARP."""
        if os.name == 'nt':  # Windows
            try:
                # First make sure the ARP entry exists
                subprocess.check_output(f"ping -n 1 -w 1000 {ip}", shell=True, stderr=subprocess.DEVNULL)
                
                arp_output = subprocess.check_output(f"arp -a {ip}", shell=True, universal_newlines=True)
                
                for line in arp_output.splitlines():
                    if ip in line:
                        # Extract MAC address which is typically the second column
                        parts = line.split()
                        if len(parts) >= 2:
                            return parts[1].strip()
            except subprocess.CalledProcessError:
                pass
        else:  
            try:
                arp_output = subprocess.check_output(f"arp -n {ip}", shell=True, universal_newlines=True)
                
                for line in arp_output.splitlines():
                    if ip in line:
                        match = re.search(r'(([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2}))', line)
                        if match:
                            return match.group(1)
            except subprocess.CalledProcessError:
                pass
        
        return "Unknown"

    def get_hostname(self, ip):
        """Resolve hostname from IP."""
        try:
            return socket.gethostbyaddr(ip)[0]
        except (socket.herror, socket.gaierror):
            return "Unknown"

    def process_ip(self, ip):
        """Process a single IP address - ping it and get its details if active."""
        if self.ping_ip(ip):
            mac = self.get_mac_from_ip(ip)
            hostname = self.get_hostname(ip)
            
            with self.lock:
                self.devices[ip] = {
                    'mac': mac,
                    'hostname': hostname,
                    'last_seen': datetime.datetime.now()
                }
            return True
        return False

    def scan_network(self):
        """Scan entire network using multithreaded ping sweep."""
        if not self.subnet:
            gateway = self.get_default_gateway()
            if gateway:
                self.subnet = self.get_subnet_from_gateway(gateway)
            else:
                print("Error: Could not determine network subnet.")
                return
        
        # Extract base network from CIDR notation (e.g., 192.168.1.0/24 -> 192.168.1)
        base_network = '.'.join(self.subnet.split('.')[0:3])
        
        print(f"Scanning network {self.subnet}...")
        active_count = 0
        
        # Create IP list (1-254 for typical home networks)
        ip_list = [f"{base_network}.{i}" for i in range(1, 255)]
        
        # Use ThreadPoolExecutor for concurrent scanning
        with ThreadPoolExecutor(max_workers=50) as executor:
            results = list(executor.map(self.process_ip, ip_list))
            active_count = results.count(True)
        
        print(f"Found {active_count} active devices.")
        return self.devices

    def display_results(self):
        """Display scan results in a table format."""
        if not self.devices:
            print("No devices found on the network.")
            return
        
        print("\n=== WiFi Network Device Scan Results ===")
        print(f"Network: {self.subnet}")
        print(f"Time: {datetime.datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
        print("\n{:<15} {:<17} {:<30} {:<20}".format("IP Address", "MAC Address", "Hostname", "Last Seen"))
        print("-" * 85)
        
        for ip, info in sorted(self.devices.items(), key=lambda x: [int(n) for n in x[0].split('.')]):
            last_seen = info['last_seen'].strftime('%Y-%m-%d %H:%M:%S')
            print("{:<15} {:<17} {:<30} {:<20}".format(
                ip, info['mac'], info['hostname'][:30], last_seen))
        
        print(f"\nTotal devices: {len(self.devices)}")

    def continuous_monitoring(self, delay=60):
        """Run continuous network monitoring."""
        try:
            while True:
                self.scan_network()
                self.display_results()
                print(f"\nWaiting {delay} seconds until next scan...")
                time.sleep(delay)
        except KeyboardInterrupt:
            print("\nMonitoring stopped by user.")

def main():
    parser = argparse.ArgumentParser(description='Enhanced WiFi User Detector')
    parser.add_argument('-s', '--subnet', help='Subnet to scan (e.g. 192.168.1.0/24)')
    parser.add_argument('-m', '--monitor', action='store_true', help='Enable continuous monitoring')
    parser.add_argument('-d', '--delay', type=int, default=60, help='Delay between scans in monitor mode (seconds)')
    parser.add_argument('-t', '--timeout', type=int, default=1, help='Ping timeout in seconds')
    args = parser.parse_args()
    
    detector = EnhancedWifiDetector(subnet=args.subnet, timeout=args.timeout)
    
    if args.monitor:
        print("Starting continuous monitoring. Press Ctrl+C to stop.")
        detector.continuous_monitoring(delay=args.delay)
    else:
        detector.scan_network()
        detector.display_results()

if __name__ == "__main__":
    main()