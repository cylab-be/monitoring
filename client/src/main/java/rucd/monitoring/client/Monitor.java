/*
 * The MIT License
 *
 * Copyright 2017 Thibault Debatty.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
package rucd.monitoring.client;

import java.util.HashMap;
import java.util.LinkedList;
import java.util.Map;

/**
 * Run all the sensors.
 *
 * @author Thibault Debatty
 */
public class Monitor {

    private final LinkedList<Sensor> sensors;

    /**
     * Initialize the monitor with the default sensors: disk usage, inodes
     * usage, reboot required.
     */
    public Monitor() {
        sensors = new LinkedList<>();
        sensors.add(new Disk());
        sensors.add(new Inodes());
        sensors.add(new Reboot());
        sensors.add(new Network());
        sensors.add(new TCP());
        sensors.add(new UDP());
    }

    /**
     * Run all the sensors and return the result as a map:
     * name of the sensor -> result of the sensor.
     * @return
     */
    public final Map<String, Object> analyze() {
        Map<String, Object> results = new HashMap<>();
        for (Sensor sensor : sensors) {
            results.put(
                    sensor.getClass().getSimpleName(),
                    sensor.run());
        }

        return results;
    }

    /*
    public void getUpdate() {
        String cmd = "gksudo cat /var/lib/update-notifier/updates-available";
        String str = executeCommand(cmd);

        String numberOnly = str.replaceAll("[^0-9]", "");
        System.out.println(str);
    }
    */
}
