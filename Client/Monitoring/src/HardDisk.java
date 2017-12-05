import java.math.BigInteger;

public class HardDisk {
	private String Name;
	private String Blocs;
	private String UsedBlocs;
	private String FreeBlocs;
	private String Used;
	private String Mounted;
	public String getName() {
		return Name;
	}
	public void setName(String name) {
		Name = name;
	}
	public String getBlocs() {
		return Blocs;
	}
	public void setBlocs(String blocs) {
		Blocs = blocs;
	}
	public String getUsedBlocs() {
		return UsedBlocs;
	}
	public void setUsedBlocs(String usedBlocs) {
		UsedBlocs = usedBlocs;
	}
	public String getFreeBlocs() {
		return FreeBlocs;
	}
	public void setFreeBlocs(String freeBlocs) {
		FreeBlocs = freeBlocs;
	}
	public String getUsed() {
		return Used;
	}
	public void setUsed(String used) {
		Used = used;
	}
	public String getMounted() {
		return Mounted;
	}
	public void setMounted(String mounted) {
		Mounted = mounted;
	}
	public HardDisk(String name, String blocs, String usedBlocs, String freeBlocs, String used, String mounted) {
		super();
		Name = name;
		Blocs = blocs;
		UsedBlocs = usedBlocs;
		FreeBlocs = freeBlocs;
		Used = used;
		Mounted = mounted;
	}
	public HardDisk(String name) {
		super();
		Name = name;
	}
	@Override
	public String toString() {
		return "HardDisk [Name=" + Name + ", Blocs=" + Blocs + ", UsedBlocs=" + UsedBlocs + ", FreeBlocs=" + FreeBlocs
				+ ", Used=" + Used + ", Mounted=" + Mounted + "]";
	}
	
}
