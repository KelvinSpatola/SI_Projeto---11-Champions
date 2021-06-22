package champions11_sql;

import java.sql.Timestamp;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.LinkedHashMap;
import java.util.LinkedList;
import java.util.List;
import java.util.Map;
import java.util.Map.Entry;
import java.util.stream.Collectors;

/**
 *
 * @author kelvi
 */
public class Util {

    public static String createSqlInsert(String ... args){
        String resultado = "INSERT INTO " + args[0] + " VALUES (";
        for (int i = 1; i < args.length; i++) {
            resultado += args[i] + (i < args.length-1 ? ", " : ");");
        }
        return resultado;
    }
    
    public static String createSqlInsertValues(boolean isLastInsert, String ... args){
        String resultado = "(";
        for (int i = 0; i < args.length; i++) {
            resultado += args[i] + (i < args.length-1 ? ", " : (isLastInsert ? ");" : "),"));
        }
        return resultado;
    }
    
    public static String[] splitParts(String str){
        String[] result = str.split(",");
        for(int i = 0; i < result.length; i++) result[i] = "'" + result[i] + "'";
        return result;
    }
    
    public static String createTimestamp(int ano, int mes, int dia, int hh){
        Date date = new Date(ano-1900, mes-1, dia, hh, 0);
        Timestamp ts = new Timestamp(date.getTime());
        SimpleDateFormat formatter = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
        return formatter.format(ts);
    }
    
    public static int createID(List<Integer> list, int range) {
        int id;
        do {
            id = randomInt(range);
        } while (list.contains(id));
        
        list.add(id);
        return id;
    }
    
    public static int randomInt(int range) {
        return (int) (Math.random() * range);
    }
    
    public static int randomInt(int min, int max) {
        float MIN = Math.min(min, max);
        float MAX = Math.max(min, max);
        return (int) (MIN + Math.random() * ((MAX-MIN)));
    }
    
    public static Map<Integer, Integer> sortByValue(Map<Integer, Integer> unsortMap, final boolean order)
    {
        List<Entry<Integer, Integer>> list = new LinkedList<>(unsortMap.entrySet());
        
        // Sorting the list based on values
        list.sort((o1, o2) -> order ? o1.getValue().compareTo(o2.getValue()) == 0
                ? o1.getKey().compareTo(o2.getKey())
                : o1.getValue().compareTo(o2.getValue()) : o2.getValue().compareTo(o1.getValue()) == 0
                        ? o2.getKey().compareTo(o1.getKey())
                        : o2.getValue().compareTo(o1.getValue()));
        return list.stream().collect(Collectors.toMap(Entry::getKey, Entry::getValue, (a, b) -> b, LinkedHashMap::new));
        
    }
}
