#include "../../trunk-recorder/plugin_manager/plugin_api.h"
#include "../../trunk-recorder/systems/system.h"
#include <boost/dll/alias.hpp> // for BOOST_DLL_ALIAS
#include <boost/foreach.hpp>

std::vector<bool> enabled_systems;

class Daily_Log : public Plugin_Api {

public:

  int call_end(Call_Data_t call_info) override {
    if (enabled_systems[call_info.short_name] == true) {
      std::stringstream dailylog; std::string dailylog2(call_info.filename);
      std::size_t found = dailylog2.find_last_of("/");
      dailylog << dailylog2.substr(0,found) << "/call_log.csv";
      std::ofstream myfile2(dailylog.str(), std::ofstream::app);
      if (myfile2.is_open()) {
        myfile2 << "\n" << call_info.start_time << "," << (time(NULL) - call_info.start_time) << "," << (int)(call_info.length + 0.5) << "," << call_info.talkgroup << "," << call_info.emergency << ",0,0,0," /* << call_info.priority << "," << call_info.duplex << "," << call_info.mode << "," */ << "," << std::fixed << std::setprecision(0) << call_info.freq << ",0" /* call_info.total_len */ << "," << call_info.error_count << "," << call_info.spike_count;
        for (std::size_t i = 0; i < call_info.transmission_source_list.size(); i++) {
          if (i != 0) {
            myfile2 << ",";
          }
          myfile2 << std::fixed << call_info.transmission_source_list[i].source << "|" << call_info.transmission_source_list[i].time << "|" << std::fixed << std::setprecision(2) << call_info.transmission_source_list[i].position;
        }
        myfile2.close();
      } else {
        return 1;
      }
    }
    return 0;
  }

  int parse_config(boost::property_tree::ptree &cfg) {
    std:string enabledfor;
    BOOST_FOREACH (boost::property_tree::ptree::value_type &node, cfg.get_child("systems")) {
      enabled_systems[node.second.get<std::string>("shortName", "")] = node.second.get<bool>("dailyLog", false);
      enabledfor += " " << node.second.get<std::string>("shortName", "");
    }
    BOOST_LOG_TRIVIAL(info) << "Daily log plugin enabled for:" << enabledfor;
    return 0;
  }

  static boost::shared_ptr<Daily_Log> create() {
    return boost::shared_ptr<Daily_Log>(
        new Daily_Log());
  }
};

BOOST_DLL_ALIAS(
    Daily_Log::create, // <-- this function is exported with...
    create_plugin             // <-- ...this alias name
)

