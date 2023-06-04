Trunk Recorder daily log plugin and live Web page
=================================================

Creates per-day call logs named call_log.csv (as opposed to individual JSON files) for [Trunk Recorder](https://github.com/robotastic/trunk-recorder) and includes a PHP Web page that can use those logs to list and play back new and past calls).

To use the plugin:
1. Make a directory named "daily_log" within Trunk Recorder's plugins directory and either copy these files or clone the repository into that directory
2. Apply the patch as follows by running __one__ of these commands from your root trunk-recorder code directory (where the CMakeLists.txt file is):
```bash
patch -p1 < plugins/d/daily_log.patch

git am < plugins/daily_log/daily_log-git.patch

```
3. Build the Trunk Recorder program normally
4. Add a `"dailyLog": true` entry to the System section of your config.json, where `60` is the number of seconds between logging entries
5. Optionally put the PHP file somewhere Web accessible and edit it

OR
1. Build Trunk Recorder normally, and run `sudo make install` at the end to install it
2. Run `cmake ./`, `make` and then `sudo make install` in this directory to build and install the plugin
3. Add to your config.json:
```yaml
 "plugins": [
    {
        "name": "daily_log",
        "library": "daily_log.so",
        "decodeRates": 60
    }]
```
4. Optionally put the PHP file somewhere Web accessible and edit it

File format is as follows:
`start_time,(real time) call length,audio length,talkgroup,emergency,priority,duplex,mode,frequency,total len,error count,spike count,source|time|position`
There is a `,source|time|position` for each transmission. `total_len` is currently always 0 and signal system, per-transmission emergency flag and tag are not saved
