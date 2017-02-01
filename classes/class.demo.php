<?php

/**
 * Generates new demos
 * @package triturn
 * @author Veljko Ilic
 * @since december 2014
 */
class DemoGenerator {
    
    private $obstacles = array();
    private $numberOfObstacles = 40;
    private $jsonObstacles;
    private $hash;
    
    public $levels = array();
    public $tree = array();
    public $count = 0;
    
    private $timeStart;
    private $timeEnd;
    
    public function __construct($map1, $map2, $map3, $map4, $map5) {
        $this->map1 = $map1;
        $this->map2 = $map2;
        $this->map3 = $map3;
        $this->map4 = $map4;
        $this->map5 = $map5;
    }
    
    public function setNumberOfObstacles($value) {
        $this->numberOfObstacles = $value;
    }
    
    public function setTimeStart($timeStart) {
        $this->timeStart = $timeStart;
    }
    
    public function setTimeEnd($timeEnd) {
        $this->timeEnd = $timeEnd;
    }
    
    public function getTimeStart() {
        return $this->timeStart;
    }
    
    public function getTimeEnd() {
        return $this->timeEnd;
    }
    
    public function createGame() {
        
        do {
            $this->generateObstacles();
        } while ($this->checkObstacles());
        
        $this->setStart();
        $this->setTimeStart(microtime(TRUE));
        
        if ($this->tableBlocked()) {
            $this->saveFailed();
        } else {
            $stop = FALSE;
            while (!$stop) {
                $stop = $this->buildNewLevel();
            }
        }
    }
    
    /**
     * Generate random obstacles
     */
    private function generateObstacles() {
        
        $obstaclePool = $this->getObstaclePool();
        $this->obstacles = array();
        while (count($this->obstacles) < $this->numberOfObstacles) {
            $index = rand(0, 226);
            if (!in_array($obstaclePool[$index][0], $this->obstacles)) {
                $this->obstacles[] = $obstaclePool[$index][0];
            }
            if (count($this->obstacles) < $this->numberOfObstacles && !in_array($obstaclePool[$index][1], $this->obstacles)) {
                $this->obstacles[] = $obstaclePool[$index][1];
            }
        }
        sort($this->obstacles);
    }
    
    private function checkObstacles() {
        $jsonObstacles = json_encode($this->obstacles);
        $hash = md5($jsonObstacles);
        
        $db = Db::getInstance();
        $sql = 'SELECT
                    id
                FROM
                    games
                WHERE
                    hash = :hash
                UNION ALL
                SELECT
                    id
                FROM
                    failed
                WHERE
                    hash = :hash
                UNION ALL
                SELECT
                    id
                FROM
                    demos
                WHERE
                    hash = :hash';
        $sth = $db->prepare($sql);
        $sth->execute(array(':hash' => $hash));
        $result = $sth->fetch();
        
        if (empty($result)) {
            $this->jsonObstacles = $jsonObstacles;
            $this->hash = $hash;
            return FALSE;
        } else {
            return TRUE;
        }
    }
    
    private function getObstaclePool() {
        return array ( 0 => array ( 0 => 3100, 1 => 3110, ), 1 => array ( 0 => 3100, 1 => 3111, ), 2 => array ( 0 => 3101, 1 => 3110, ), 3 => array ( 0 => 3101, 1 => 3111, ), 4 => array ( 0 => 3101, 1 => 4100, ), 5 => array ( 0 => 3110, 1 => 3211, ), 6 => array ( 0 => 3200, 1 => 3210, ), 7 => array ( 0 => 3200, 1 => 3211, ), 8 => array ( 0 => 3201, 1 => 3210, ), 9 => array ( 0 => 3201, 1 => 3211, ), 10 => array ( 0 => 3201, 1 => 4200, ), 11 => array ( 0 => 3210, 1 => 3311, ), 12 => array ( 0 => 3300, 1 => 3310, ), 13 => array ( 0 => 3300, 1 => 3311, ), 14 => array ( 0 => 3301, 1 => 3310, ), 15 => array ( 0 => 3301, 1 => 3311, ), 16 => array ( 0 => 3301, 1 => 4300, ), 17 => array ( 0 => 3310, 1 => 3411, ), 18 => array ( 0 => 3400, 1 => 3410, ), 19 => array ( 0 => 3400, 1 => 3411, ), 20 => array ( 0 => 3401, 1 => 3410, ), 21 => array ( 0 => 3401, 1 => 3411, ), 22 => array ( 0 => 3401, 1 => 4400, ), 23 => array ( 0 => 3410, 1 => 3511, ), 24 => array ( 0 => 3500, 1 => 3510, ), 25 => array ( 0 => 3500, 1 => 3511, ), 26 => array ( 0 => 3501, 1 => 3510, ), 27 => array ( 0 => 3501, 1 => 3511, ), 28 => array ( 0 => 3501, 1 => 4500, ), 29 => array ( 0 => 4100, 1 => 4110, ), 30 => array ( 0 => 4100, 1 => 4111, ), 31 => array ( 0 => 4101, 1 => 4110, ), 32 => array ( 0 => 4101, 1 => 4111, ), 33 => array ( 0 => 4101, 1 => 5100, ), 34 => array ( 0 => 4110, 1 => 4211, ), 35 => array ( 0 => 4200, 1 => 4210, ), 36 => array ( 0 => 4200, 1 => 4211, ), 37 => array ( 0 => 4201, 1 => 4210, ), 38 => array ( 0 => 4201, 1 => 4211, ), 39 => array ( 0 => 4201, 1 => 5200, ), 40 => array ( 0 => 4210, 1 => 4311, ), 41 => array ( 0 => 4300, 1 => 4310, ), 42 => array ( 0 => 4300, 1 => 4311, ), 43 => array ( 0 => 4301, 1 => 4310, ), 44 => array ( 0 => 4301, 1 => 4311, ), 45 => array ( 0 => 4301, 1 => 5300, ), 46 => array ( 0 => 4310, 1 => 4411, ), 47 => array ( 0 => 4400, 1 => 4410, ), 48 => array ( 0 => 4400, 1 => 4411, ), 49 => array ( 0 => 4401, 1 => 4410, ), 50 => array ( 0 => 4401, 1 => 4411, ), 51 => array ( 0 => 4401, 1 => 5400, ), 52 => array ( 0 => 4410, 1 => 4511, ), 53 => array ( 0 => 4500, 1 => 4510, ), 54 => array ( 0 => 4500, 1 => 4511, ), 55 => array ( 0 => 4501, 1 => 4510, ), 56 => array ( 0 => 4501, 1 => 4511, ), 57 => array ( 0 => 4501, 1 => 5500, ), 58 => array ( 0 => 5100, 1 => 5110, ), 59 => array ( 0 => 5100, 1 => 5111, ), 60 => array ( 0 => 5101, 1 => 5110, ), 61 => array ( 0 => 5101, 1 => 5111, ), 62 => array ( 0 => 5101, 1 => 6100, ), 63 => array ( 0 => 5110, 1 => 5211, ), 64 => array ( 0 => 5200, 1 => 5210, ), 65 => array ( 0 => 5200, 1 => 5211, ), 66 => array ( 0 => 5201, 1 => 5210, ), 67 => array ( 0 => 5201, 1 => 5211, ), 68 => array ( 0 => 5201, 1 => 6200, ), 69 => array ( 0 => 5210, 1 => 5311, ), 70 => array ( 0 => 5300, 1 => 5310, ), 71 => array ( 0 => 5300, 1 => 5311, ), 72 => array ( 0 => 5301, 1 => 5310, ), 73 => array ( 0 => 5301, 1 => 5311, ), 74 => array ( 0 => 5301, 1 => 6300, ), 75 => array ( 0 => 5310, 1 => 5411, ), 76 => array ( 0 => 5400, 1 => 5410, ), 77 => array ( 0 => 5400, 1 => 5411, ), 78 => array ( 0 => 5401, 1 => 5410, ), 79 => array ( 0 => 5401, 1 => 5411, ), 80 => array ( 0 => 5401, 1 => 6400, ), 81 => array ( 0 => 5410, 1 => 5511, ), 82 => array ( 0 => 5500, 1 => 5510, ), 83 => array ( 0 => 5500, 1 => 5511, ), 84 => array ( 0 => 5501, 1 => 5510, ), 85 => array ( 0 => 5501, 1 => 5511, ), 86 => array ( 0 => 5501, 1 => 6500, ), 87 => array ( 0 => 6100, 1 => 6110, ), 88 => array ( 0 => 6100, 1 => 6111, ), 89 => array ( 0 => 6101, 1 => 6110, ), 90 => array ( 0 => 6101, 1 => 6111, ), 91 => array ( 0 => 6101, 1 => 7100, ), 92 => array ( 0 => 6110, 1 => 6211, ), 93 => array ( 0 => 6200, 1 => 6210, ), 94 => array ( 0 => 6200, 1 => 6211, ), 95 => array ( 0 => 6201, 1 => 6210, ), 96 => array ( 0 => 6201, 1 => 6211, ), 97 => array ( 0 => 6201, 1 => 7200, ), 98 => array ( 0 => 6210, 1 => 6311, ), 99 => array ( 0 => 6300, 1 => 6310, ), 100 => array ( 0 => 6300, 1 => 6311, ), 101 => array ( 0 => 6301, 1 => 6310, ), 102 => array ( 0 => 6301, 1 => 6311, ), 103 => array ( 0 => 6301, 1 => 7300, ), 104 => array ( 0 => 6310, 1 => 6411, ), 105 => array ( 0 => 6400, 1 => 6410, ), 106 => array ( 0 => 6400, 1 => 6411, ), 107 => array ( 0 => 6401, 1 => 6410, ), 108 => array ( 0 => 6401, 1 => 6411, ), 109 => array ( 0 => 6401, 1 => 7400, ), 110 => array ( 0 => 6410, 1 => 6511, ), 111 => array ( 0 => 6500, 1 => 6510, ), 112 => array ( 0 => 6500, 1 => 6511, ), 113 => array ( 0 => 6501, 1 => 6510, ), 114 => array ( 0 => 6501, 1 => 6511, ), 115 => array ( 0 => 6501, 1 => 7500, ), 116 => array ( 0 => 7100, 1 => 7110, ), 117 => array ( 0 => 7100, 1 => 7111, ), 118 => array ( 0 => 7101, 1 => 7110, ), 119 => array ( 0 => 7101, 1 => 7111, ), 120 => array ( 0 => 7101, 1 => 8100, ), 121 => array ( 0 => 7110, 1 => 7211, ), 122 => array ( 0 => 7200, 1 => 7210, ), 123 => array ( 0 => 7200, 1 => 7211, ), 124 => array ( 0 => 7201, 1 => 7210, ), 125 => array ( 0 => 7201, 1 => 7211, ), 126 => array ( 0 => 7201, 1 => 8200, ), 127 => array ( 0 => 7210, 1 => 7311, ), 128 => array ( 0 => 7300, 1 => 7310, ), 129 => array ( 0 => 7300, 1 => 7311, ), 130 => array ( 0 => 7301, 1 => 7310, ), 131 => array ( 0 => 7301, 1 => 7311, ), 132 => array ( 0 => 7301, 1 => 8300, ), 133 => array ( 0 => 7310, 1 => 7411, ), 134 => array ( 0 => 7400, 1 => 7410, ), 135 => array ( 0 => 7400, 1 => 7411, ), 136 => array ( 0 => 7401, 1 => 7410, ), 137 => array ( 0 => 7401, 1 => 7411, ), 138 => array ( 0 => 7401, 1 => 8400, ), 139 => array ( 0 => 7410, 1 => 7511, ), 140 => array ( 0 => 7500, 1 => 7510, ), 141 => array ( 0 => 7500, 1 => 7511, ), 142 => array ( 0 => 7501, 1 => 7510, ), 143 => array ( 0 => 7501, 1 => 7511, ), 144 => array ( 0 => 7501, 1 => 8500, ), 145 => array ( 0 => 8100, 1 => 8110, ), 146 => array ( 0 => 8100, 1 => 8111, ), 147 => array ( 0 => 8101, 1 => 8110, ), 148 => array ( 0 => 8101, 1 => 8111, ), 149 => array ( 0 => 8101, 1 => 9100, ), 150 => array ( 0 => 8110, 1 => 8211, ), 151 => array ( 0 => 8200, 1 => 8210, ), 152 => array ( 0 => 8200, 1 => 8211, ), 153 => array ( 0 => 8201, 1 => 8210, ), 154 => array ( 0 => 8201, 1 => 8211, ), 155 => array ( 0 => 8201, 1 => 9200, ), 156 => array ( 0 => 8210, 1 => 8311, ), 157 => array ( 0 => 8300, 1 => 8310, ), 158 => array ( 0 => 8300, 1 => 8311, ), 159 => array ( 0 => 8301, 1 => 8310, ), 160 => array ( 0 => 8301, 1 => 8311, ), 161 => array ( 0 => 8301, 1 => 9300, ), 162 => array ( 0 => 8310, 1 => 8411, ), 163 => array ( 0 => 8400, 1 => 8410, ), 164 => array ( 0 => 8400, 1 => 8411, ), 165 => array ( 0 => 8401, 1 => 8410, ), 166 => array ( 0 => 8401, 1 => 8411, ), 167 => array ( 0 => 8401, 1 => 9400, ), 168 => array ( 0 => 8410, 1 => 8511, ), 169 => array ( 0 => 8500, 1 => 8510, ), 170 => array ( 0 => 8500, 1 => 8511, ), 171 => array ( 0 => 8501, 1 => 8510, ), 172 => array ( 0 => 8501, 1 => 8511, ), 173 => array ( 0 => 8501, 1 => 9500, ), 174 => array ( 0 => 9100, 1 => 9110, ), 175 => array ( 0 => 9100, 1 => 9111, ), 176 => array ( 0 => 9101, 1 => 9110, ), 177 => array ( 0 => 9101, 1 => 9111, ), 178 => array ( 0 => 9101, 1 => 10100, ), 179 => array ( 0 => 9110, 1 => 9211, ), 180 => array ( 0 => 9200, 1 => 9210, ), 181 => array ( 0 => 9200, 1 => 9211, ), 182 => array ( 0 => 9201, 1 => 9210, ), 183 => array ( 0 => 9201, 1 => 9211, ), 184 => array ( 0 => 9201, 1 => 10200, ), 185 => array ( 0 => 9210, 1 => 9311, ), 186 => array ( 0 => 9300, 1 => 9310, ), 187 => array ( 0 => 9300, 1 => 9311, ), 188 => array ( 0 => 9301, 1 => 9310, ), 189 => array ( 0 => 9301, 1 => 9311, ), 190 => array ( 0 => 9301, 1 => 10300, ), 191 => array ( 0 => 9310, 1 => 9411, ), 192 => array ( 0 => 9400, 1 => 9410, ), 193 => array ( 0 => 9400, 1 => 9411, ), 194 => array ( 0 => 9401, 1 => 9410, ), 195 => array ( 0 => 9401, 1 => 9411, ), 196 => array ( 0 => 9401, 1 => 10400, ), 197 => array ( 0 => 9410, 1 => 9511, ), 198 => array ( 0 => 9500, 1 => 9510, ), 199 => array ( 0 => 9500, 1 => 9511, ), 200 => array ( 0 => 9501, 1 => 9510, ), 201 => array ( 0 => 9501, 1 => 9511, ), 202 => array ( 0 => 9501, 1 => 10500, ), 203 => array ( 0 => 10100, 1 => 10110, ), 204 => array ( 0 => 10100, 1 => 10111, ), 205 => array ( 0 => 10101, 1 => 10110, ), 206 => array ( 0 => 10101, 1 => 10111, ), 207 => array ( 0 => 10110, 1 => 10211, ), 208 => array ( 0 => 10200, 1 => 10210, ), 209 => array ( 0 => 10200, 1 => 10211, ), 210 => array ( 0 => 10201, 1 => 10210, ), 211 => array ( 0 => 10201, 1 => 10211, ), 212 => array ( 0 => 10210, 1 => 10311, ), 213 => array ( 0 => 10300, 1 => 10310, ), 214 => array ( 0 => 10300, 1 => 10311, ), 215 => array ( 0 => 10301, 1 => 10310, ), 216 => array ( 0 => 10301, 1 => 10311, ), 217 => array ( 0 => 10310, 1 => 10411, ), 218 => array ( 0 => 10400, 1 => 10410, ), 219 => array ( 0 => 10400, 1 => 10411, ), 220 => array ( 0 => 10401, 1 => 10410, ), 221 => array ( 0 => 10401, 1 => 10411, ), 222 => array ( 0 => 10410, 1 => 10511, ), 223 => array ( 0 => 10500, 1 => 10510, ), 224 => array ( 0 => 10500, 1 => 10511, ), 225 => array ( 0 => 10501, 1 => 10510, ), 226 => array ( 0 => 10501, 1 => 10511, ), );
    }
    
    private function setStart() {
        $this->levels[] = $this->getStart();
        $this->tree[] = array(NULL);
    }
    
    private function getStart() {
        return array ( 0 => array ( 0 => 1101, 1 => 1110, 2 => 2100, 3 => 2101, 4 => 2110, ), 1 => array ( 0 => 1101, 1 => 1110, 2 => 2100, 3 => 2101, 4 => 2111, ), 2 => array ( 0 => 1101, 1 => 1110, 2 => 2100, 3 => 2110, 4 => 2111, ), 3 => array ( 0 => 1101, 1 => 1110, 2 => 2100, 3 => 2110, 4 => 2211, ), 4 => array ( 0 => 1101, 1 => 1111, 2 => 2100, 3 => 2101, 4 => 2110, ), 5 => array ( 0 => 1101, 1 => 1111, 2 => 2100, 3 => 2101, 4 => 2111, ), 6 => array ( 0 => 1101, 1 => 1111, 2 => 2100, 3 => 2110, 4 => 2111, ), 7 => array ( 0 => 1101, 1 => 1111, 2 => 2100, 3 => 2110, 4 => 2211, ), 8 => array ( 0 => 1101, 1 => 2100, 2 => 2101, 3 => 2110, 4 => 2111, ), 9 => array ( 0 => 1101, 1 => 2100, 2 => 2101, 3 => 2110, 4 => 2211, ), 10 => array ( 0 => 1101, 1 => 2100, 2 => 2110, 3 => 2111, 4 => 2211, ), 11 => array ( 0 => 1101, 1 => 2100, 2 => 2110, 3 => 2200, 4 => 2211, ), 12 => array ( 0 => 1101, 1 => 2100, 2 => 2110, 3 => 2201, 4 => 2211, ), 13 => array ( 0 => 1201, 1 => 1210, 2 => 2110, 3 => 2200, 4 => 2211, ), 14 => array ( 0 => 1201, 1 => 1210, 2 => 2200, 3 => 2201, 4 => 2210, ), 15 => array ( 0 => 1201, 1 => 1210, 2 => 2200, 3 => 2201, 4 => 2211, ), 16 => array ( 0 => 1201, 1 => 1210, 2 => 2200, 3 => 2210, 4 => 2211, ), 17 => array ( 0 => 1201, 1 => 1210, 2 => 2200, 3 => 2210, 4 => 2311, ), 18 => array ( 0 => 1201, 1 => 1211, 2 => 2110, 3 => 2200, 4 => 2211, ), 19 => array ( 0 => 1201, 1 => 1211, 2 => 2200, 3 => 2201, 4 => 2210, ), 20 => array ( 0 => 1201, 1 => 1211, 2 => 2200, 3 => 2201, 4 => 2211, ), 21 => array ( 0 => 1201, 1 => 1211, 2 => 2200, 3 => 2210, 4 => 2211, ), 22 => array ( 0 => 1201, 1 => 1211, 2 => 2200, 3 => 2210, 4 => 2311, ), 23 => array ( 0 => 1201, 1 => 2100, 2 => 2110, 3 => 2200, 4 => 2211, ), 24 => array ( 0 => 1201, 1 => 2101, 2 => 2110, 3 => 2200, 4 => 2211, ), 25 => array ( 0 => 1201, 1 => 2110, 2 => 2200, 3 => 2201, 4 => 2211, ), 26 => array ( 0 => 1201, 1 => 2110, 2 => 2200, 3 => 2210, 4 => 2211, ), 27 => array ( 0 => 1201, 1 => 2200, 2 => 2201, 3 => 2210, 4 => 2211, ), 28 => array ( 0 => 1201, 1 => 2200, 2 => 2201, 3 => 2210, 4 => 2311, ), 29 => array ( 0 => 1201, 1 => 2200, 2 => 2210, 3 => 2211, 4 => 2311, ), 30 => array ( 0 => 1201, 1 => 2200, 2 => 2210, 3 => 2300, 4 => 2311, ), 31 => array ( 0 => 1201, 1 => 2200, 2 => 2210, 3 => 2301, 4 => 2311, ), 32 => array ( 0 => 1301, 1 => 1310, 2 => 2210, 3 => 2300, 4 => 2311, ), 33 => array ( 0 => 1301, 1 => 1310, 2 => 2300, 3 => 2301, 4 => 2310, ), 34 => array ( 0 => 1301, 1 => 1310, 2 => 2300, 3 => 2301, 4 => 2311, ), 35 => array ( 0 => 1301, 1 => 1310, 2 => 2300, 3 => 2310, 4 => 2311, ), 36 => array ( 0 => 1301, 1 => 1310, 2 => 2300, 3 => 2310, 4 => 2411, ), 37 => array ( 0 => 1301, 1 => 1311, 2 => 2210, 3 => 2300, 4 => 2311, ), 38 => array ( 0 => 1301, 1 => 1311, 2 => 2300, 3 => 2301, 4 => 2310, ), 39 => array ( 0 => 1301, 1 => 1311, 2 => 2300, 3 => 2301, 4 => 2311, ), 40 => array ( 0 => 1301, 1 => 1311, 2 => 2300, 3 => 2310, 4 => 2311, ), 41 => array ( 0 => 1301, 1 => 1311, 2 => 2300, 3 => 2310, 4 => 2411, ), 42 => array ( 0 => 1301, 1 => 2200, 2 => 2210, 3 => 2300, 4 => 2311, ), 43 => array ( 0 => 1301, 1 => 2201, 2 => 2210, 3 => 2300, 4 => 2311, ), 44 => array ( 0 => 1301, 1 => 2210, 2 => 2300, 3 => 2301, 4 => 2311, ), 45 => array ( 0 => 1301, 1 => 2210, 2 => 2300, 3 => 2310, 4 => 2311, ), 46 => array ( 0 => 1301, 1 => 2300, 2 => 2301, 3 => 2310, 4 => 2311, ), 47 => array ( 0 => 1301, 1 => 2300, 2 => 2301, 3 => 2310, 4 => 2411, ), 48 => array ( 0 => 1301, 1 => 2300, 2 => 2310, 3 => 2311, 4 => 2411, ), 49 => array ( 0 => 1301, 1 => 2300, 2 => 2310, 3 => 2400, 4 => 2411, ), 50 => array ( 0 => 1301, 1 => 2300, 2 => 2310, 3 => 2401, 4 => 2411, ), 51 => array ( 0 => 1401, 1 => 1410, 2 => 2310, 3 => 2400, 4 => 2411, ), 52 => array ( 0 => 1401, 1 => 1410, 2 => 2400, 3 => 2401, 4 => 2410, ), 53 => array ( 0 => 1401, 1 => 1410, 2 => 2400, 3 => 2401, 4 => 2411, ), 54 => array ( 0 => 1401, 1 => 1410, 2 => 2400, 3 => 2410, 4 => 2411, ), 55 => array ( 0 => 1401, 1 => 1410, 2 => 2400, 3 => 2410, 4 => 2511, ), 56 => array ( 0 => 1401, 1 => 1411, 2 => 2310, 3 => 2400, 4 => 2411, ), 57 => array ( 0 => 1401, 1 => 1411, 2 => 2400, 3 => 2401, 4 => 2410, ), 58 => array ( 0 => 1401, 1 => 1411, 2 => 2400, 3 => 2401, 4 => 2411, ), 59 => array ( 0 => 1401, 1 => 1411, 2 => 2400, 3 => 2410, 4 => 2411, ), 60 => array ( 0 => 1401, 1 => 1411, 2 => 2400, 3 => 2410, 4 => 2511, ), 61 => array ( 0 => 1401, 1 => 2300, 2 => 2310, 3 => 2400, 4 => 2411, ), 62 => array ( 0 => 1401, 1 => 2301, 2 => 2310, 3 => 2400, 4 => 2411, ), 63 => array ( 0 => 1401, 1 => 2310, 2 => 2400, 3 => 2401, 4 => 2411, ), 64 => array ( 0 => 1401, 1 => 2310, 2 => 2400, 3 => 2410, 4 => 2411, ), 65 => array ( 0 => 1401, 1 => 2400, 2 => 2401, 3 => 2410, 4 => 2411, ), 66 => array ( 0 => 1401, 1 => 2400, 2 => 2401, 3 => 2410, 4 => 2511, ), 67 => array ( 0 => 1401, 1 => 2400, 2 => 2410, 3 => 2411, 4 => 2511, ), 68 => array ( 0 => 1401, 1 => 2400, 2 => 2410, 3 => 2500, 4 => 2511, ), 69 => array ( 0 => 1401, 1 => 2400, 2 => 2410, 3 => 2501, 4 => 2511, ), 70 => array ( 0 => 1501, 1 => 1510, 2 => 2410, 3 => 2500, 4 => 2511, ), 71 => array ( 0 => 1501, 1 => 1510, 2 => 2500, 3 => 2501, 4 => 2510, ), 72 => array ( 0 => 1501, 1 => 1510, 2 => 2500, 3 => 2501, 4 => 2511, ), 73 => array ( 0 => 1501, 1 => 1510, 2 => 2500, 3 => 2510, 4 => 2511, ), 74 => array ( 0 => 1501, 1 => 1511, 2 => 2410, 3 => 2500, 4 => 2511, ), 75 => array ( 0 => 1501, 1 => 1511, 2 => 2500, 3 => 2501, 4 => 2510, ), 76 => array ( 0 => 1501, 1 => 1511, 2 => 2500, 3 => 2501, 4 => 2511, ), 77 => array ( 0 => 1501, 1 => 1511, 2 => 2500, 3 => 2510, 4 => 2511, ), 78 => array ( 0 => 1501, 1 => 2400, 2 => 2410, 3 => 2500, 4 => 2511, ), 79 => array ( 0 => 1501, 1 => 2401, 2 => 2410, 3 => 2500, 4 => 2511, ), 80 => array ( 0 => 1501, 1 => 2410, 2 => 2500, 3 => 2501, 4 => 2511, ), 81 => array ( 0 => 1501, 1 => 2410, 2 => 2500, 3 => 2510, 4 => 2511, ), 82 => array ( 0 => 1501, 1 => 2500, 2 => 2501, 3 => 2510, 4 => 2511, ), 83 => array ( 0 => 2100, 1 => 2101, 2 => 2110, 3 => 2111, 4 => 2211, ), 84 => array ( 0 => 2100, 1 => 2101, 2 => 2110, 3 => 2200, 4 => 2211, ), 85 => array ( 0 => 2100, 1 => 2101, 2 => 2110, 3 => 2201, 4 => 2211, ), 86 => array ( 0 => 2100, 1 => 2110, 2 => 2111, 3 => 2200, 4 => 2211, ), 87 => array ( 0 => 2100, 1 => 2110, 2 => 2111, 3 => 2201, 4 => 2211, ), 88 => array ( 0 => 2100, 1 => 2110, 2 => 2200, 3 => 2201, 4 => 2211, ), 89 => array ( 0 => 2100, 1 => 2110, 2 => 2200, 3 => 2210, 4 => 2211, ), 90 => array ( 0 => 2100, 1 => 2110, 2 => 2201, 3 => 2210, 4 => 2211, ), 91 => array ( 0 => 2101, 1 => 2110, 2 => 2111, 3 => 2200, 4 => 2211, ), 92 => array ( 0 => 2101, 1 => 2110, 2 => 2111, 3 => 2201, 4 => 2211, ), 93 => array ( 0 => 2101, 1 => 2110, 2 => 2200, 3 => 2201, 4 => 2211, ), 94 => array ( 0 => 2101, 1 => 2110, 2 => 2200, 3 => 2210, 4 => 2211, ), 95 => array ( 0 => 2101, 1 => 2110, 2 => 2201, 3 => 2210, 4 => 2211, ), 96 => array ( 0 => 2110, 1 => 2200, 2 => 2201, 3 => 2210, 4 => 2211, ), 97 => array ( 0 => 2110, 1 => 2200, 2 => 2210, 3 => 2211, 4 => 2311, ), 98 => array ( 0 => 2110, 1 => 2201, 2 => 2210, 3 => 2211, 4 => 2311, ), 99 => array ( 0 => 2200, 1 => 2201, 2 => 2210, 3 => 2211, 4 => 2311, ), 100 => array ( 0 => 2200, 1 => 2201, 2 => 2210, 3 => 2300, 4 => 2311, ), 101 => array ( 0 => 2200, 1 => 2201, 2 => 2210, 3 => 2301, 4 => 2311, ), 102 => array ( 0 => 2200, 1 => 2210, 2 => 2211, 3 => 2300, 4 => 2311, ), 103 => array ( 0 => 2200, 1 => 2210, 2 => 2211, 3 => 2301, 4 => 2311, ), 104 => array ( 0 => 2200, 1 => 2210, 2 => 2300, 3 => 2301, 4 => 2311, ), 105 => array ( 0 => 2200, 1 => 2210, 2 => 2300, 3 => 2310, 4 => 2311, ), 106 => array ( 0 => 2200, 1 => 2210, 2 => 2301, 3 => 2310, 4 => 2311, ), 107 => array ( 0 => 2201, 1 => 2210, 2 => 2211, 3 => 2300, 4 => 2311, ), 108 => array ( 0 => 2201, 1 => 2210, 2 => 2211, 3 => 2301, 4 => 2311, ), 109 => array ( 0 => 2201, 1 => 2210, 2 => 2300, 3 => 2301, 4 => 2311, ), 110 => array ( 0 => 2201, 1 => 2210, 2 => 2300, 3 => 2310, 4 => 2311, ), 111 => array ( 0 => 2201, 1 => 2210, 2 => 2301, 3 => 2310, 4 => 2311, ), 112 => array ( 0 => 2210, 1 => 2300, 2 => 2301, 3 => 2310, 4 => 2311, ), 113 => array ( 0 => 2210, 1 => 2300, 2 => 2310, 3 => 2311, 4 => 2411, ), 114 => array ( 0 => 2210, 1 => 2301, 2 => 2310, 3 => 2311, 4 => 2411, ), 115 => array ( 0 => 2300, 1 => 2301, 2 => 2310, 3 => 2311, 4 => 2411, ), 116 => array ( 0 => 2300, 1 => 2301, 2 => 2310, 3 => 2400, 4 => 2411, ), 117 => array ( 0 => 2300, 1 => 2301, 2 => 2310, 3 => 2401, 4 => 2411, ), 118 => array ( 0 => 2300, 1 => 2310, 2 => 2311, 3 => 2400, 4 => 2411, ), 119 => array ( 0 => 2300, 1 => 2310, 2 => 2311, 3 => 2401, 4 => 2411, ), 120 => array ( 0 => 2300, 1 => 2310, 2 => 2400, 3 => 2401, 4 => 2411, ), 121 => array ( 0 => 2300, 1 => 2310, 2 => 2400, 3 => 2410, 4 => 2411, ), 122 => array ( 0 => 2300, 1 => 2310, 2 => 2401, 3 => 2410, 4 => 2411, ), 123 => array ( 0 => 2301, 1 => 2310, 2 => 2311, 3 => 2400, 4 => 2411, ), 124 => array ( 0 => 2301, 1 => 2310, 2 => 2311, 3 => 2401, 4 => 2411, ), 125 => array ( 0 => 2301, 1 => 2310, 2 => 2400, 3 => 2401, 4 => 2411, ), 126 => array ( 0 => 2301, 1 => 2310, 2 => 2400, 3 => 2410, 4 => 2411, ), 127 => array ( 0 => 2301, 1 => 2310, 2 => 2401, 3 => 2410, 4 => 2411, ), 128 => array ( 0 => 2310, 1 => 2400, 2 => 2401, 3 => 2410, 4 => 2411, ), 129 => array ( 0 => 2310, 1 => 2400, 2 => 2410, 3 => 2411, 4 => 2511, ), 130 => array ( 0 => 2310, 1 => 2401, 2 => 2410, 3 => 2411, 4 => 2511, ), 131 => array ( 0 => 2400, 1 => 2401, 2 => 2410, 3 => 2411, 4 => 2511, ), 132 => array ( 0 => 2400, 1 => 2401, 2 => 2410, 3 => 2500, 4 => 2511, ), 133 => array ( 0 => 2400, 1 => 2401, 2 => 2410, 3 => 2501, 4 => 2511, ), 134 => array ( 0 => 2400, 1 => 2410, 2 => 2411, 3 => 2500, 4 => 2511, ), 135 => array ( 0 => 2400, 1 => 2410, 2 => 2411, 3 => 2501, 4 => 2511, ), 136 => array ( 0 => 2400, 1 => 2410, 2 => 2500, 3 => 2501, 4 => 2511, ), 137 => array ( 0 => 2400, 1 => 2410, 2 => 2500, 3 => 2510, 4 => 2511, ), 138 => array ( 0 => 2400, 1 => 2410, 2 => 2501, 3 => 2510, 4 => 2511, ), 139 => array ( 0 => 2401, 1 => 2410, 2 => 2411, 3 => 2500, 4 => 2511, ), 140 => array ( 0 => 2401, 1 => 2410, 2 => 2411, 3 => 2501, 4 => 2511, ), 141 => array ( 0 => 2401, 1 => 2410, 2 => 2500, 3 => 2501, 4 => 2511, ), 142 => array ( 0 => 2401, 1 => 2410, 2 => 2500, 3 => 2510, 4 => 2511, ), 143 => array ( 0 => 2401, 1 => 2410, 2 => 2501, 3 => 2510, 4 => 2511, ), 144 => array ( 0 => 2410, 1 => 2500, 2 => 2501, 3 => 2510, 4 => 2511, ), );
    }
    
    private function buildNewLevel() {
        
        $newLevel = array();
        $newTree = array();
        
        $ratings = array(
            1 => array(),
            2 => array(),
            3 => array(),
            4 => array(),
            5 => array()
        );
        
        $lastLevel = $this->levels[$this->count];
        $lastLevelCount = count($lastLevel);
        
        for ($i = 5; $i > 0; $i--) {
            
            switch ($i) {
                case 1:
                    for ($j = 0; $j < $lastLevelCount; $j++) {
                        $elements = $lastLevel[$j];
                        
                        for ($k = 0; $k < count($elements); $k++) {
                            if ($elements[$k] > 10900) {
                                continue;
                            }
                            if (isset($this->map1[$elements[$k]])) {
                                
                                $maps = $this->map1[$elements[$k]];
                                $maps = $this->filterMaps($maps, $elements);
                                
                                if (count($maps) > 0) {
                                    $base = array_diff($elements, array($elements[$k]));
                                    foreach ($maps as $map) {
                                        $newItem = array_merge($base, $map);
                                        sort($newItem);
                                        if (!$this->exists($newItem, $newLevel)) {
                                            if ($this->isEnd($newItem)) {
                                                $this->save($newItem, $j);
                                                return TRUE;
                                            }
                                            if ($this->compact($newItem, $ratings, count($newLevel), 1)) {
                                                $newLevel[] = $newItem;
                                                $newTree[] = $j;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    
                    break;
                case 2:
                    
                    for ($j = 0; $j < $lastLevelCount; $j++) {
                        $elements = $lastLevel[$j];
                        
                        $temp = array();
                        for ($k = 0; $k < count($elements) - 1; $k++) {
                            if ($elements[$k] > 10900) {
                                continue;
                            }
                            for ($l = $k + 1; $l < count($elements); $l++) {
                                if ($elements[$l] > 10900) {
                                    continue;
                                }
                                $temp = array($elements[$k], $elements[$l]);
                                sort($temp);
                                
                                if (isset($this->map2[$temp[0]][$temp[1]])) {
                                    $maps = $this->map2[$temp[0]][$temp[1]];
                                    $base = array_diff($elements, $temp);
                                    $maps = $this->filterMaps($maps, $elements);
                                    foreach ($maps as $map) {
                                        $newItem = array_merge($base, $map);
                                        sort($newItem);
                                        if (!$this->exists($newItem, $newLevel)) {
                                            if ($this->isEnd($newItem)) {
                                                $this->save($newItem, $j);
                                                return TRUE;
                                            }
                                            if ($this->compact($newItem, $ratings, count($newLevel), 2)) {
                                                $newLevel[] = $newItem;
                                                $newTree[] = $j;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    
                    break;
                case 3:
                    
                    for ($j = 0; $j < $lastLevelCount; $j++) {
                        $elements = $lastLevel[$j];
                        
                        $temp = array();
                        for ($k = 0; $k < count($elements) - 2; $k++) {
                            if ($elements[$k] > 10900) {
                                continue;
                            }
                            for ($l = $k + 1; $l < count($elements) - 1; $l++) {
                                if ($elements[$l] > 10900) {
                                    continue;
                                }
                                for ($m = $l + 1; $m < count($elements); $m++) {
                                    if ($elements[$m] > 10900) {
                                        continue;
                                    }
                                    
                                    $temp = array($elements[$k], $elements[$l], $elements[$m]);
                                    sort($temp);
                                    
                                    if (isset($this->map3[$temp[0]][$temp[1]][$temp[2]])) {
                                        $maps = $this->map3[$temp[0]][$temp[1]][$temp[2]];
                                        $base = array_diff($elements, $temp);
                                        $maps = $this->filterMaps($maps, $elements);
                                        foreach ($maps as $map) {
                                            $newItem = array_merge($base, $map);
                                            sort($newItem);
                                            if (!$this->exists($newItem, $newLevel)) {
                                                if ($this->isEnd($newItem)) {
                                                    $this->save($newItem, $j);
                                                    return TRUE;
                                                }
                                                if ($this->compact($newItem, $ratings, count($newLevel), 3)) {
                                                    $newLevel[] = $newItem;
                                                    $newTree[] = $j;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    
                    break;
                case 4:
                    
                    for ($j = 0; $j < $lastLevelCount; $j++) {
                        $elements = $lastLevel[$j];
                        
                        $temp = array();
                        for ($k = 0; $k < count($elements) - 3; $k++) {
                            if ($elements[$k] > 10900) {
                                continue;
                            }
                            for ($l = $k + 1; $l < count($elements) - 2; $l++) {
                                if ($elements[$l] > 10900) {
                                    continue;
                                }
                                for ($m = $l + 1; $m < count($elements) - 1; $m++) {
                                    if ($elements[$m] > 10900) {
                                        continue;
                                    }
                                    for ($n = $m + 1; $n < count($elements); $n++) {
                                        if ($elements[$n] > 10900) {
                                            continue;
                                        }
                                        
                                        $temp = array($elements[$k], $elements[$l], $elements[$m], $elements[$n]);
                                        sort($temp);
                                        if (isset($this->map4[$temp[0]][$temp[1]][$temp[2]][$temp[3]])) {
                                            $maps = $this->map4[$temp[0]][$temp[1]][$temp[2]][$temp[3]];
                                            $base = array_diff($elements, $temp);
                                            $maps = $this->filterMaps($maps, $elements);
                                            foreach ($maps as $map) {
                                                $newItem = array_merge($base, $map);
                                                sort($newItem);
                                                if (!$this->exists($newItem, $newLevel)) {
                                                    if ($this->isEnd($newItem)) {
                                                        $this->save($newItem, $j);
                                                        return TRUE;
                                                    }
                                                    if ($this->compact($newItem, $ratings, count($newLevel), 4)) {
                                                        $newLevel[] = $newItem;
                                                        $newTree[] = $j;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    
                    break;
                case 5:
                    
                    for ($j = 0; $j < $lastLevelCount; $j++) {
                        $elements = $lastLevel[$j];
                        
                        $temp = array();
                        for ($k = 0; $k < count($elements) - 4; $k++) {
                            if ($elements[$k] > 10900) {
                                continue;
                            }
                            for ($l = $k + 1; $l < count($elements) - 3; $l++) {
                                if ($elements[$l] > 10900) {
                                    continue;
                                }
                                for ($m = $l + 1; $m < count($elements) - 2; $m++) {
                                    if ($elements[$m] > 10900) {
                                        continue;
                                    }
                                    for ($n = $m + 1; $n < count($elements) - 1; $n++) {
                                        if ($elements[$n] > 10900) {
                                            continue;
                                        }
                                        for ($o = $n + 1; $o < count($elements); $o++) {
                                            if ($elements[$o] > 10900) {
                                                continue;
                                            }
                                            
                                            $temp = array($elements[$k], $elements[$l], $elements[$m], $elements[$n], $elements[$o]);
                                            sort($temp);
                                            if (isset($this->map5[$temp[0]][$temp[1]][$temp[2]][$temp[3]][$temp[4]])) {
                                                $maps = $this->map5[$temp[0]][$temp[1]][$temp[2]][$temp[3]][$temp[4]];
                                                $maps = $this->filterMaps($maps, $elements);
                                                
                                                foreach ($maps as $map) {
                                                    if (!$this->exists($map, $newLevel)) {
                                                        if ($this->isEnd($map)) {
                                                            $this->save($map, $j);
                                                            return TRUE;
                                                        }
                                                        if ($this->compact($map, $ratings, count($newLevel), 5)) {
                                                            $newLevel[] = $map;
                                                            $newTree[] = $j;
                                                        }
                                                    }
                                                }
                                                
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        
                    }
                    
                    break;
            }
        }
        if (empty($newLevel) || $this->count == 25) {
            $this->saveFailed();
            return TRUE;
        } else {
            $this->filterLagged($newLevel, $newTree, $ratings);
            $this->levels[] = $newLevel;
            $this->tree[] = $newTree;
            $this->count++;
            return FALSE;
        }
        
    }
    
    private function filterMaps($maps, $item) {
        
        $filtered = array();
        
        foreach ($maps as $map) {
            $insert = TRUE;
            foreach ($map as $m) {
                if (in_array($m, $this->obstacles) || ($m < 11000 && in_array($m, $item)) || ($m < 2000)) {
                    $insert = FALSE;
                    break;
                }
            }
            if ($insert) {
                $filtered[] = $map;
            }
        }
        return $filtered;
    }
    
    private function exists($item, $newLevel) {
        if (in_array($item, $newLevel)) {
            return TRUE;
        }
        foreach ($this->levels as $level) {
            if (in_array($item, $level)) {
                return TRUE;
                break;
            }
        }
        return FALSE;
    }
    
    private function isEnd($elements) {
        foreach ($elements as $element) {
            if ($element < 11000) {
                return FALSE;
                break;
            }
        }
        return TRUE;
    }
    
    private function save($lastMove, $index) {
        
        $this->setTimeEnd(microtime(TRUE));
        $timeUsage = $this->getTimeEnd() - $this->getTimeStart();
        $memoryUsage = round(memory_get_usage(TRUE) / 1048576);
        
        $moves = array();
        $moves[] = $lastMove;
        
        for ($i = $this->count; $i > -1; $i--) {
            $moves[] = $this->levels[$i][$index];
            if ($i > 0) { 
                $index = $this->tree[$i][$index];
            }
        }
        
        $moves = array_reverse($moves);
        $jsonMoves = $this->prepareMoves($moves);
        
        $db = Db::getInstance();
        
        $sql = 'INSERT INTO
                    `demos`
                    (`obstacles`, `moves`, `raw_moves`, `level`, `hash`, `time_usage`, `memory_usage`, `created`, `number_of_obstacles`, `min_moves`)
                VALUES
                    (:obstacles, :moves, :raw_moves, :level, :hash, :time_usage, :memory_usage, NOW(), :number_of_obstacles, :min_moves)';
        
        $level = $this->numberOfObstacles / 4 - 3 + 2 * ($this->count + 1 - 9);
        if ($level > $this->count + 1) {
            $level = $this->count + 1;
        }
        
        $params = array( 
            'obstacles' => $this->jsonObstacles,
            'moves' => $jsonMoves,
            'raw_moves' => json_encode($moves, JSON_NUMERIC_CHECK),
            'level' => $level,
            'hash' => $this->hash,
            'time_usage' => $timeUsage,
            'memory_usage' => $memoryUsage,
            'number_of_obstacles' => $this->numberOfObstacles,
            'min_moves' => $this->count + 1
        ); 
        
        $sth = $db->prepare($sql);
        $inserted = $sth->execute($params);
        return $inserted;
    }
    
    private function saveFailed() {
        
        $this->setTimeEnd(microtime(TRUE));
        $timeUsage = $this->getTimeEnd() - $this->getTimeStart();
        $memoryUsage = round(memory_get_usage(TRUE) / 1048576);
        
        $db = Db::getInstance();
        $sql = 'INSERT INTO
                    `failed`
                    (`obstacles`, `time_usage`, `memory_usage`, `hash`, `created`)
                VALUES
                    (:obstacles, :time_usage, :memory_usage, :hash, NOW())';
        $params = array(
            ':obstacles' => $this->jsonObstacles,
            ':time_usage' => $timeUsage,
            ':memory_usage' => $memoryUsage,
            ':hash' => $this->hash
        ); 
        $sth = $db->prepare($sql);
        $inserted = $sth->execute($params);
        return $inserted;
    }
    
    public function prepareMoves($moves) {
        
        $prepared = array();
        $prepared[] = $moves[0];
        
        for ($i = 1; $i < count($moves); $i++) {
            
            $temp = array();
            $diffA = array_diff($moves[$i - 1], $moves[$i]);
            $diffB = array_diff($moves[$i], $moves[$i - 1]);
            
            foreach ($moves[$i] as $field) {
                if ($field > 10900) {
                    $diffB[] = $field;
                }
            }
            $diffB = array_unique($diffB);
            
            sort($diffA);
            sort($diffB);
            
            switch (count($diffA)) {
                case 1:
                case 2:
                    $prepared[] = array_merge($diffA, array($diffB[0]));
                    $maps = FALSE;
                    break;
                case 3:
                    $maps = $this->map3[$diffA[0]][$diffA[1]][$diffA[2]];
                    break;
                case 4:
                    $maps = $this->map4[$diffA[0]][$diffA[1]][$diffA[2]][$diffA[3]];
                    break;
                case 5:
                    $maps = $this->map5[$diffA[0]][$diffA[1]][$diffA[2]][$diffA[3]][$diffA[4]];
                    break;
            }
            
            if (!$maps) {
                continue;
            }
            
            $fields = array();
            foreach ($maps as $map) {
                $fields = array_merge($fields, $map);
            }
            $countFields = array_count_values($fields);
            
            foreach ($diffB as $field) {
                if (isset($countFields[$field]) && $countFields[$field] == 1) {
                    $prepared[] = array_merge($diffA, array($field));
                    break;
                }
            }
        }
        
        return json_encode($prepared, JSON_NUMERIC_CHECK);
    }
    
    public function compact($item, &$ratings, $index, $size) {
        
        $x = array();
        $y = array();
        
        foreach ($item as $element) {
            $parts = str_split($element);
            if (count($parts) == 5) {
                $x[] = intval($parts[0] . $parts[1]);
                $y[] = intval($parts[2]);
            } else {
                $x[] = intval($parts[0]);
                $y[] = intval($parts[1]);
            }
        }
        
        $a = array_count_values($x);
        if (isset($a[1]) ||
            (isset($a[2]) && $a[2] > 4) ||
            ($this->count > 0 && (isset($a[2]) && $a[2] > 1)) ||
            ($this->count > 7 && (isset($a[2]) || isset($a[3])))) {
            return FALSE;
        }
        
        $dx = max($x) - min($x);
        $dy = max($y) - min($y);
        
        if ($dx > 3 || ($dx == 3 && $dy > 0) || $dy > 3 || ($dy == 3 && $dx > 0)) {
            return FALSE;
        } else {
            
            if (isset($this->map5[$item[0]][$item[1]][$item[2]][$item[3]][$item[4]])) {
                $is5 = 1;
            } else {
                $is5 = 0;
            }
            $ratings[round(array_sum($y) / 5)][$index] = array_sum($x) - 5 * ($dx + $dy - $is5);
            
            return TRUE;
        }
        
    }
    
    private function filterLagged(&$newLevel, &$newTree, $ratings) {
        
        foreach ($ratings as $y => $rates) {
            asort($rates);
            $i = count($rates);
            
            if ($i > 1000) {
                foreach ($rates as $index => $value) {
                    unset($newLevel[$index]);
                    unset($newTree[$index]);
                    $i--;
                    if ($i == 1000) {
                        break;
                    }
                }
            }
        }
        $newLevel = array_values($newLevel);
        $newTree = array_values($newTree);
    }
    
    public function startGenerator() {
        
        $fields = array();
        for ($i = 1; $i < 6; $i++) {
            $fields[] = intval('1' . $i . '01');
            $fields[] = intval('1' . $i . '10');
            $fields[] = intval('1' . $i . '11');
        }
        for ($i = 1; $i < 6; $i++) {
            $fields[] = intval('2' . $i . '00');
            $fields[] = intval('2' . $i . '01');
            $fields[] = intval('2' . $i . '10');
            $fields[] = intval('2' . $i . '11');
        }
        
        $start = array();
        $len = count($fields);
        $test = array();
        for ($i = 0; $i < $len - 4; $i++) {
            $test[0] = 0;
            if ($i < 15) {
                $test[0] = 1;
            }
            for ($j = $i + 1; $j < $len - 3; $j++) {
                $test[1] = 0;
                if ($j < 15) {
                    $test[1] = 1;
                }
                for ($k = $j + 1; $k < $len - 2; $k++) {
                    if ($k < 15 && array_sum($test) > 1) {  
                        continue;
                    }
                    for ($l = $k + 1; $l < $len - 1; $l++) {
                        for ($m = $l + 1; $m < $len; $m++) {
                            
                            if (isset($this->map5[$fields[$i]][$fields[$j]][$fields[$k]][$fields[$l]][$fields[$m]])) {
                                $start[] = array($fields[$i], $fields[$j], $fields[$k], $fields[$l], $fields[$m]);
                            }
                            
                        }
                    }
                }
            }
        }
        
        return $start;
        
    }
    
    public function fixLevel() {
        $db = Db::getInstance();
            
        $sql = 'SELECT * FROM games';
        $statement = $db->prepare($sql);
        $statement->execute();
        $games = $statement->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($games as $game) {
            $moves = json_decode($game['moves'], TRUE);
            $sql = 'UPDATE games SET count = :count, level = :level WHERE id = :id';
            $statement = $db->prepare($sql);
            $statement->execute(array('count' => count($moves), 'level' => count($moves) - 1, 'id' => $game['id']));
        }
    }
    
    public function tableBlocked() {
        
        $squares = array(
            1 => array(),
            2 => array(),
            3 => array(),
            4 => array(),
            5 => array()
        );
        
        foreach ($this->obstacles as $obstacle) {
            $start = substr($obstacle, 0, -2);
            $end = substr($obstacle, -2);
            $x = substr($start, 0, -1);
            $y = substr($start, -1);
            
            $squares[$y][$x][] = $end;
        }
        
        foreach ($squares as $y => $yAxis) {
            foreach ($yAxis as $x => $square) {
                if (count($square) < 2) {
                    unset($squares[$y][$x]);
                }
            }
        }
        
        for ($i = 1; $i < 6; $i++) {
            if (count($squares[$i]) < 1) {
                return FALSE;
            }
        }
        
        $blocks = array();
        
        foreach ($squares as $y => $yAxis) {
            $blocks[$y] = array();
            $block = array();
            $lastSquare = NULL;
            $lastX = 0;
            foreach ($yAxis as $x => $square) {
                
                if (empty($block) || ($x == $lastX + 1 && $this->connectedHorizontally($lastSquare, $square))) {
                    $block[] = $x;
                } else {
                    $blocks[$y][] = $block;
                    $block = array();
                    $block[] = $x;
                }
                
                $lastSquare = $square;
                $lastX = $x;
            }
            
            if (!empty($block)) {
                $blocks[$y][] = $block;
            }
        }
        
        $connections = array();
        
        for ($y = 1; $y < 5; $y++) {
            foreach ($blocks[$y] as $bottomIndex => $bottomBlock) {
                foreach ($blocks[$y + 1] as $topIndex => $topBlock) {
                    if ($this->blocksConnected($bottomBlock, $topBlock, $y, $squares)) {
                        $connections[$y][$bottomIndex][] = $topIndex;
                    }
                }
            }
        }
        
        for ($i = 1; $i < 5; $i++) {
            if (!isset($connections[$i])) {
                return FALSE;
            }
        }
        
        foreach ($connections as $y => &$conns) {
            $keys = array_keys($conns);
            for ($i = 0; $i < count($keys) - 1; $i++) {
                for ($j = $i + 1; $j < count($keys); $j++) {
                    $intersection = array_intersect($conns[$keys[$i]], $conns[$keys[$j]]);
                    if (!empty($intersection)) {
                        $merge = array_unique(array_merge($conns[$keys[$i]], $conns[$keys[$j]]));
                        $conns[$i] = $merge;
                        $conns[$j] = $merge;
                    }
                }
            }
        }
        
        foreach ($connections[1] as $indexes1) {
            $indexes = $indexes1;
            $i = 2;
            while ($i < 5 && !empty($indexes)) {
                $temp = array();
                foreach ($indexes as $j) {
                    if (isset($connections[$i][$j])) {
                        if ($i == 4) {
                            return TRUE;
                        }
                        $temp = array_merge($temp, $connections[$i][$j]);
                    }
                }
                $indexes = $temp;
                $i++;
            }
        }
        
        return FALSE;
    }
    
    private function connectedHorizontally($leftSquare, $rightSquare) {
        if (
            count($leftSquare) > 2
            || count($rightSquare) > 2
            || in_array('01', $leftSquare)
            || in_array('00', $rightSquare)
            || (in_array('10', $leftSquare) && in_array('10', $rightSquare))
            || (in_array('11', $leftSquare) && in_array('11', $rightSquare))
        ) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    private function connectedVertically($bottomSquare, $topSquare, $y, $squares) {
        $diff = $bottomSquare - $topSquare;
        $squareA = $squares[$y][$bottomSquare];
        $squareB = $squares[$y + 1][$topSquare];
        switch ($diff) {
            case 0:
                if (
                    count($squareA) > 2
                    || count($squareB) > 2
                    || in_array('10', $squareA)
                    || in_array('11', $squareB)
                    || (in_array('00', $squareA) && in_array('00', $squareB))
                    || (in_array('01', $squareA) && in_array('01', $squareB))
                ) {
                    return TRUE;
                }
                break;
            case 1:
                if (
                    (in_array('00', $squareA) || in_array('10', $squareA))
                    && (in_array('01', $squareB) || in_array('11', $squareB))
                ) {
                    return TRUE;
                }
                break;
            case -1:
                if (
                    (in_array('01', $squareA) || in_array('10', $squareA))
                    && (in_array('00', $squareB) || in_array('11', $squareB))
                ) {
                    return TRUE;
                }
                break;
        }
        return FALSE;
    }
    
    private function blocksConnected($bottomBlock, $topBlock, $y, $squares) {
        foreach ($bottomBlock as $bottomSquare) {
            foreach ($topBlock as $topSquare) {
                if ($this->connectedVertically($bottomSquare, $topSquare, $y, $squares)) {
                    return TRUE;
                }
            }
        }
        return FALSE;
    }
    
}

?>