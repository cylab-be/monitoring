package rucd.monitoring.client;

import java.io.BufferedReader;
import java.io.File;
import java.io.IOException;
import java.io.InputStreamReader;
import java.net.InetAddress;
import java.net.NetworkInterface;
import java.net.SocketException;
import java.net.UnknownHostException;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.List;
import com.fasterxml.jackson.annotation.JsonView;
public class Monitor {
	@JsonView(Views.Normal.class)
	private List<HardDisk> Disks = new ArrayList<HardDisk>(15);
	@JsonView(Views.Normal.class)
	private int[] Update = new int[2];
	@JsonView(Views.Normal.class)
	private boolean IsRebootRequired;
	@JsonView(Views.Normal.class)
	private String mac ="1";

	public Monitor() {
		super();
	}

	public String executeCommand(String command) {
		StringBuffer output = new StringBuffer();
		Process p;

		try {
			p = Runtime.getRuntime().exec(command);
			p.waitFor();
			BufferedReader reader = new BufferedReader(new InputStreamReader(p.getInputStream()));
			String line = "";
			while ((line = reader.readLine()) != null) {
				output.append(line + "\n");
			}

		} catch (IOException e) {
			e.printStackTrace();
		} catch (InterruptedException e) {
			e.printStackTrace();
		} catch (Exception e) {
			e.printStackTrace();
		}
		return output.toString();
	}

	public void getUpdate() {
		String cmd = "gksudo cat /var/lib/update-notifier/updates-available";
		String str = executeCommand(cmd);

		String numberOnly= str.replaceAll("[^0-9]", "");
		System.out.println(str);
	}
	public boolean rebootRequired() {
		File f = new File("/var/run/reboot-required");
		if (f.exists()) {
			IsRebootRequired=true;
		}
		IsRebootRequired=false;
		return IsRebootRequired;
	}


	@Override
	public String toString() {
		return "Monitor [Disks=" + Disks + ", Update=" + Arrays.toString(Update) + ", IsRebootRequired="
				+ IsRebootRequired + ", mac=" + mac + "]";
	}

	public void getHardDisksState() {
		for (int i = 0; i < 6; i++) {
			String str = executeCommand("./hardDisk" + i + ".sh");
			String[] lines = str.split("\r\n|\r|\n");
			for (int j = 1; j < lines.length; j++) {
				int index = j-1;
				switch (i) {
				case 0:
					HardDisk hd = new HardDisk(lines[j]);
					Disks.add(hd);
					break;
				case 1:
					Disks.get(index).setBlocs(lines[j]);
					break;
				case 2:
					Disks.get(index).setUsedBlocs(lines[j]);
					break;
				case 3:
					Disks.get(index).setFreeBlocs(lines[j]);
					break;
				case 4:
					Disks.get(index).setUsed(lines[j]);
					break;
				case 5:
					Disks.get(index).setMounted(lines[j]);
					break;
				default:
					break;
				}
			}
		}
		/*for (HardDisk hardDisk : Disks) {
			System.out.println(hardDisk);
		}*/
	}
}
