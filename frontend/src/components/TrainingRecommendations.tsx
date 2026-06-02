import { TrainingRecommendation } from '@/types';

interface Props { recommendations: TrainingRecommendation[]; }

export default function TrainingRecommendations({ recommendations }: Props) {
  if (!recommendations.length) {
    return (
      <div className="text-center py-10 text-gray-400">
        <div className="text-4xl mb-2">🎉</div>
        <p>No training flags — all values are performing above threshold.</p>
      </div>
    );
  }

  return (
    <div className="grid gap-4 sm:grid-cols-2">
      {recommendations.map((prog) => (
        <div
          key={prog.id}
          className="bg-white border border-orange-200 rounded-xl p-5 shadow-sm hover:shadow-md transition-shadow"
        >
          <div className="flex items-start gap-3">
            <div className="w-10 h-10 rounded-lg bg-orange-100 flex items-center justify-center text-orange-600 flex-shrink-0 text-xl">
              📚
            </div>
            <div className="flex-1 min-w-0">
              <h4 className="font-semibold text-gray-900 truncate">{prog.title}</h4>
              {prog.description && (
                <p className="text-sm text-gray-500 mt-1 line-clamp-2">{prog.description}</p>
              )}
              {prog.triggered_by && prog.triggered_by.length > 0 && (
                <div className="mt-2 flex flex-wrap gap-1">
                  {prog.triggered_by.map((val) => (
                    <span key={val} className="bg-orange-50 text-orange-700 text-xs px-2 py-0.5 rounded-full border border-orange-200">
                      {val}
                    </span>
                  ))}
                </div>
              )}
              <p className="text-xs text-gray-400 mt-2">
                Trigger threshold: score &lt; {prog.trigger_threshold}
              </p>
            </div>
          </div>
        </div>
      ))}
    </div>
  );
}
